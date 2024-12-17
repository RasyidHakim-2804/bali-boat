<?php

namespace App\Http\Controllers\Api\V1\FastBoatOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RefundOrderRequest as ApiRefundOrderRequest;
use App\Models\FastBoatOrder;
use App\Models\RefundRequestPin;
use App\Models\User;
use App\Notifications\FastBoatOrder\FastBoatOrderRefundPinNotification;
use App\Notifications\FastBoatOrder\FastBoatOrderRefundRequestNotification;
use App\Services\Order\OrderRefundService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FastBoatOrderRefundController extends Controller
{
    use JsonResponsesTrait;
    
    public function request(User $user, FastBoatOrder $fastBoatOrder)
    {
        if ($fastBoatOrder->status != 'paid') return $this->errorValidationResponse('order_id', 'Order is invalid.');

        DB::beginTransaction();
        try {
            $orderRefundService = (new OrderRefundService);

            $orderRefund    = $orderRefundService->getOrderRefund($fastBoatOrder, 'fast_boat');
            $refundRequest  = $orderRefundService->getRefundRequest($fastBoatOrder, 'fast_boat');

            if (isset($refundRequest)) $refundRequest->delete();
        
            $newRefundRequest = $orderRefundService->createRefundRequestPin($fastBoatOrder, 'fast_boat');

            $this->sendEmailPin($fastBoatOrder, $newRefundRequest->pin);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function refund(ApiRefundOrderRequest $request, User $user,  FastBoatOrder $fastBoatOrder)
    {
        if ($fastBoatOrder->status != 'paid') return $this->errorValidationResponse('order_id', 'Order is invalid.');

        $refundRequest = RefundRequestPin::where('fast_boat_order_id', $fastBoatOrder->id)->where('pin', $request->pin)->orderBy('id', 'DESC')->first();

        if (!isset($refundRequest)) return abort(422, 'The pin is invalid.');

        if ((isset($refundRequest)) && ($refundRequest->expired_at < Carbon::now())) return abort(422, 'The pin is expired. Please make a new pin.');

        DB::beginTransaction();
        try {
            $orderRefund = (new OrderRefundService)->refund($fastBoatOrder, 'fast_boat');

            $fastBoatOrder->status = 'refunding';

            $fastBoatOrder->save();

            $this->sendEmailRefund($fastBoatOrder, $orderRefund->payout_url);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }
    
    protected function sendEmailPin($order, $pin)
    {
        $user = $order->user;

        $data               = [];
        $data['language']   = config('app.locale');
        $data['first_name'] = $user->first_name;
        $data['order_uuid'] = $order->uuid;
        $data['pin']        = $pin;
        
        $user->notify(new FastBoatOrderRefundPinNotification($data));
    }

    protected function sendEmailRefund($order, $refund_url)
    {
        $user = $order->user;

        $data               = [];
        $data['language']   = config('app.locale');
        $data['first_name'] = $user->first_name;
        $data['order_uuid'] = $order->uuid;
        $data['refund_url'] = $refund_url;
        
        $user->notify(new FastBoatOrderRefundRequestNotification($data));
    }
}