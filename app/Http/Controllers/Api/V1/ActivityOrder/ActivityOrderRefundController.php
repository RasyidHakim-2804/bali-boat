<?php

namespace App\Http\Controllers\Api\V1\ActivityOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RefundOrderRequest as ApiRefundOrderRequest;
use App\Models\ActivityOrder;
use App\Models\RefundRequestPin;
use App\Models\User;
use App\Notifications\ActivityOrder\ActivityOrderRefundPinNotification;
use App\Notifications\ActivityOrder\ActivityOrderRefundRequestNotification;
use App\Services\Order\OrderRefundService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityOrderRefundController extends Controller
{
    use JsonResponsesTrait;
    
    public function request(User $user, ActivityOrder $activityOrder)
    {
        if ($activityOrder->status != 'paid') return $this->errorValidationResponse('order_id', 'Order is invalid.');

        DB::beginTransaction();
        try {
            $orderRefundService = (new OrderRefundService);

            $orderRefund    = $orderRefundService->getOrderRefund($activityOrder, 'activity');
            $refundRequest  = $orderRefundService->getRefundRequest($activityOrder, 'activity');

            if (isset($refundRequest)) $refundRequest->delete();
        
            $newRefundRequest = $orderRefundService->createRefundRequestPin($activityOrder, 'activity');

            $this->sendEmailPin($activityOrder, $newRefundRequest->pin);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function refund(ApiRefundOrderRequest $request, User $user,  ActivityOrder $activityOrder)
    {
        if ($activityOrder->status != 'paid') return $this->errorValidationResponse('order_id', 'Order is invalid.');

        $refundRequest = RefundRequestPin::where('activity_order_id', $activityOrder->id)->where('pin', $request->pin)->orderBy('id', 'DESC')->first();

        if (!isset($refundRequest)) return abort(422, 'The pin is invalid.');

        if ((isset($refundRequest)) && ($refundRequest->expired_at < Carbon::now())) return abort(422, 'The pin is expired. Please make a new pin.');

        DB::beginTransaction();
        try {
            $orderRefund = (new OrderRefundService)->refund($activityOrder, 'activity');

            $activityOrder->status = 'refunding';

            $activityOrder->save();

            $this->sendEmailRefund($activityOrder, $orderRefund->payout_url);

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
        
        $user->notify(new ActivityOrderRefundPinNotification($data));
    }

    protected function sendEmailRefund($order, $refund_url)
    {
        $user = $order->user;

        $data               = [];
        $data['language']   = config('app.locale');
        $data['first_name'] = $user->first_name;
        $data['order_uuid'] = $order->uuid;
        $data['refund_url'] = $refund_url;
        
        $user->notify(new ActivityOrderRefundRequestNotification($data));
    }
}