<?php

namespace App\Http\Controllers\Api\V1\Order;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\RefundOrderRequest;
use App\Mail\Order\OrderRefundPinNotificationMail;
use App\Mail\Order\RefundOrderNotificationMail;
use App\Models\Order;
use App\Models\OrderRefund;
use App\Models\RefundRequestPin;
use App\Services\Order\OrderRefundService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderRefundController extends Controller
{
    use JsonResponsesTrait;
    
    public function request(Order $order)
    {
        if ($order->status != 'declined') return $this->errorValidationResponse('order_id', 'Order is invalid.');
        
        $orderRefund  = OrderRefund::where('order_id', $order->id)->orderBy('id', 'DESC')->first();

        if ((isset($orderRefund)) && ($orderRefund->status == 'claimed')) $this->errorMessage('The refund already success.');

        if ((isset($orderRefund)) && ($orderRefund->status == 'submitted') && ($orderRefund->expired_at >= Carbon::now())) $this->errorMessage('The refund already requested. Please check on your email.');

        $refundRequest  = RefundRequestPin::where('order_id', $order->id)->orderBy('id', 'DESC')->first();

        if ((isset($refundRequest)) && ($refundRequest->expired_at >= Carbon::now())) return $this->errorMessage('The pin already requested. PLease try again in 1 minute.');

        DB::beginTransaction();
        try {
            if (isset($refundRequest)) $refundRequest->delete();
        
            $newRefundRequest              = new RefundRequestPin();
            $newRefundRequest->order_id    = $order->id;
            $newRefundRequest->pin         = $this->generatePin($order);
            $newRefundRequest->expired_at  = Carbon::now()->addMinutes(1);

            $this->sendEmailPin($order, $newRefundRequest->pin);

            $newRefundRequest->save();

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function refund(RefundOrderRequest $request, Order $order)
    {
        if ($order->status != 'declined') return $this->errorValidationResponse('order_id', 'Order is invalid.');

        $refundRequest = RefundRequestPin::where('order_id', $order->id)->orderBy('id', 'DESC')->first();

        if ((isset($refundRequest)) && ($refundRequest->expired_at < Carbon::now())) return $this->errorMessage('The pin is expired. Please make a new pin.');

        DB::beginTransaction();
        try {
            (new OrderRefundService)->refund($order, $request);

            $order->status = 'refunding';

            $order->save();

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }
    
    protected function sendEmailPin($order, $pin)
    {
        $data               = [];
        $data['first_name'] = $order->guest_first_name;
        $data['pin']        = $pin;
        
        Mail::to($order->guest_email)->send((new OrderRefundPinNotificationMail($data)));
    }

    protected function sendEmailRefund($order, $refund_url)
    {
        $data               = [];
        $data['first_name'] = $order->guest_first_name;
        $data['refund_url'] = $refund_url;
        
        Mail::to($order->guest_email)->send((new RefundOrderNotificationMail($data)));
    }

    protected function generatePin($order)
    {
        do {
            $pin = rand(000000, 999999);
            $samePin = RefundRequestPin::where('pin', $pin)->where('order_id', $order->id)->count();
        } while (($samePin > 0) || (strlen($pin) != 6));

        return $pin;
    }
}