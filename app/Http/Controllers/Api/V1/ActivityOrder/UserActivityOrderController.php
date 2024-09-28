<?php

namespace App\Http\Controllers\Api\V1\ActivityOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ActivityOrder\StoreUserActivityOrderRequest;
use App\Http\Resources\ActivityOrder\ActivityOrderResource;
use App\Http\Resources\ActivityOrder\ListActivityOrderResource;
use App\Models\Activity;
use App\Models\ActivityOrder;
use App\Models\ActivityVariant;
use App\Models\Promo;
use App\Models\User;
use App\Notifications\ActivityOrder\ActivityOrderCreatedNotification;
use App\Services\ActivityOrder\ActivityOrderDetailService;
use App\Services\ActivityOrder\ActivityOrderRegistrationService;
use App\Services\ActivityOrder\ActivityOrderService;
use App\Services\Order\OrderRefundService;
use App\Services\Payment\PaymentService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class UserActivityOrderController extends Controller
{
    use JsonResponsesTrait;

    protected $products;

    protected $variants;

    protected $totalProductQuantity;

    public function index(User $user)
    {
        return ListActivityOrderResource::collection(ActivityOrder::where('user_id', $user->id)->with('details')->withCount('forms')->latest()->paginate(25));
    }
    
    public function show(User $user, ActivityOrder $activityOrder)
    {
        $order = new ActivityOrderResource($activityOrder);

        return $this->successGet($order);
    }
    
    public function store(StoreUserActivityOrderRequest $request, User $user)
    {
        $user = request()->user();

        // Validation
        $promo = $this->verifyPromo($request->promo_uuid);

        $this->setGlobalVariable($request);

        $this->checkProductAvailability($request);

        $this->checkFormValidity($request);

        $this->checkFormQuantity($request);

        DB::beginTransaction();
        try {
            // Create order
            $order = (new ActivityOrderService)->create($request, $this->products, $this->variants, $user, $promo);

            (new ActivityOrderDetailService)->create($order->id, $request->products, $this->products, $this->variants);

            (new ActivityOrderRegistrationService)->create($order->id, $request->forms);

            $responseData = ['order_uuid' => $order->uuid];

            (new PaymentService)->prepare()->pay($order, 'activity');

            $responseData['payment_url'] = $order->payment->url;

            $this->sendEmail($order);

            // Return result

            DB::commit();

            return $this->successStoreWithData($responseData);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('order')->info(PHP_EOL . '' . PHP_EOL .'Error New Order (unlogged user)'. PHP_EOL .'' . $e->getMessage());

            // return $e;
            return $e;
        }
    }

    public function cancel(User $user, ActivityOrder $activityOrder)
    {
        DB::beginTransaction();
        try {
            if ($activityOrder->status != 'waiting_for_payment') return $this->errorValidationResponse('order_id', 'Order is invalid.');

            $activityOrder->status = 'canceled';

            $activityOrder->save();

            (new PaymentService)->prepare()->expire($activityOrder, 'activity');

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function refund(Request $request, User $user, ActivityOrder $activityOrder)
    {
        if ($activityOrder->status != 'paid') return $this->errorValidationResponse('order_id', 'Order is invalid.');

        if (!$activityOrder->payment) return $this->errorValidationResponse('order_id', 'Order is not paid in system. Please contact us by email or phone for refund this order.');

        DB::beginTransaction();
        try {
            (new OrderRefundService)->refund($activityOrder, 'activity', $request);

            $activityOrder->status = 'refunding';

            $activityOrder->save();

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }
    
    protected function setGlobalVariable($request)
    {
        $this->totalProductQuantity = 0;
        
        foreach ($request->products as $key => $product) {
            $this->products[$key] = Activity::where('id', $product['id'])->first();
            $this->variants[$key] = ActivityVariant::where('id', $product['variant_id'])->first();

            $this->totalProductQuantity = $this->totalProductQuantity + $product['quantity'];
        }
    }
    
    protected function checkProductAvailability($request)
    {
        foreach ($request->products as $key => $product) {
            if (($this->products[$key]->is_varied == 1) && (!isset($this->variants[$key]))) {
                if (config('app.locale') == 'id') return abort(422, "Varian produk " . $key + 1 . " wajib diisi.");

                return abort(422, "The product variant " . ($key + 1) .  " is required.");
            }
    
            $dataProduct = $this->products[$key];
    
            if (isset($this->variants[$key])) $dataProduct = $this->variants[$key];
    
            if ($dataProduct->min_order > $product['quantity']) {
                if (config('app.locale') == 'id') return abort(422, "Jumlah produk " . $key + 1 . " dibawah dari minimum order.");

                return abort(422, "The product " . ($key + 1) .  " quantity should exceeded min order.");
            }
        }
    }

    protected function checkFormQuantity($request)
    {
        foreach ($request->products as $key => $product) {
            $countCheck = 0;

            foreach ($request->forms as $key2 => $form) {
                $check = 0;

                if (isset($product['variant_id'])) {
                    // $check = (($form['product_id'] == $product['id']) && (($form['variant_id'] == $product['variant_id'])) && (($form['activity_date'] == $product['activity_date'])))
                    $check = (($form['product_id'] == $product['id']) && (($form['variant_id'] == $product['variant_id'])))
                        ? 1 : 0;
                } else {
                    // $check = ($form['product_id'] == $product['id']  && (($form['activity_date'] == $product['activity_date'])))
                    $check = ($form['product_id'] == $product['id'])
                        ? 1 : 0;
                }

                $countCheck = $countCheck + $check;
            }
            
            if ($countCheck != $product['quantity']) {
                if (config('app.locale') == 'id') return abort(422, "Jumlah produk " . $key + 1 . " tidak sama dengan jumlah formulir terkait.");
                
                return abort(422, "The quantity of product " . ($key + 1) .  " not equal to the form quantity.");
            }

            // if ($countCheck != $product['quantity']) return abort(422, "The quantity of product " . ($key + 1) .  " not equal to the form quantity.");
        }
    }

    protected function checkFormValidity($request)
    {
        foreach ($request->forms as $key => $form) {
            $productParent = array_filter($request->products, function($product) use($form) {
                return $product['id'] == $form['product_id'];
            });

            if ((isset($productParent[0]['variant_id'])) && (!isset($form['variant_id']))) {
                if (config('app.locale') == 'id') return abort(422, "Varian produk pada formulir ' . $key + 1 . ' wajib diisi.");

                return abort(422, 'The forms ' . $key + 1 . ' variant_id is required.');
            }
        }
    }
    
    protected function calculateTotalPurchased($products)
    {
        $totalPurchased = 0;
        
        foreach ($products as $key => $product) {
            $productPrice = $this->products[$key]->price;
            if (isset($this->variants[$key]))  $productPrice = $this->variants[$key]->price;
            $totalProductPurchased = ($product['quantity'] * $productPrice);
            $totalPurchased        = $totalPurchased + $totalProductPurchased;
        }
        
        return $totalPurchased;
    }
    
    protected function sendEmail($order)
    {
        $user = request()->user();
        
        $data                   = [];
        $data['language']       = config('app.locale');
        $data['first_name']     = $user->first_name;
        $data['order_url']      = env('FRONTEND_URL') . '/user/orders/tours/' . $order->uuid;
        $data['payment_url']    = $order->payment->url;
        
        $user->notify(new ActivityOrderCreatedNotification($data));
    }

    // protected function sendAdminEmail($order)
    // {
    //     $email = env('MAIL_FROM_ADDRESS');
        
    //     $data                         = [];
    //     $data['first_name']           = 'Admin';
    //     $data['order_url']            = env('FRONTEND_URL') . '/admin/orders/' . $order->uuid . '/detail';
        
    //     Mail::to($email)->send((new OrderNeedAdminConfirmationNotificationMail($data)));
    // }
    
    protected function verifyPromo($promoUuid)
    {
        if (!isset($promoUuid)) return null;
        
        $promo = Promo::where('uuid', $promoUuid)->where('uuid', '!=', null)->first();

        if (!$promo) {
            if (config('app.locale') == 'id') return abort(422, 'Promo tidak valid.');

            return abort(422, 'The promo is invalid.');
        }

        if ($promo->expired_at < now()) {
            if (config('app.locale') == 'id') return abort(422, 'Promo sudah berakhir.');

            return abort(422, 'The promo is expired.');
        }
        
        $orderPromoAppliedCounted = 0;
        
        if ($promo->duration == 'once') $orderPromoAppliedCounted = ActivityOrder::where('user_id', auth()->user()->id)->where('promo_id', $promo->id)->count();
        
        if ($orderPromoAppliedCounted > 0) {
            if (config('app.locale') == 'id') return abort(422, 'Promo telah terpakai.');

            return abort(422, 'The promo already used.');
        }

        return $promo;
    }
}