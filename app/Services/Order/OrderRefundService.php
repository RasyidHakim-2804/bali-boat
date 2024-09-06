<?php

namespace App\Services\Order;

use App\Models\OrderRefund;
use App\Models\RefundRequestPin;
use App\Services\Payment\PaymentPayoutService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class OrderRefundService
{
    public function refund($order, $type)
    {
        $user   = $order->user;
        $email  = $user->email;

        try {
            $xenditPayoutLink = (new PaymentPayoutService)->prepare()->payout($email, $order->uuid, $order->grand_total_purchased, 'refund');

            $orderRefund                        = new OrderRefund();
            $orderRefund->xendit_payout_link_id = $xenditPayoutLink->id;
            $orderRefund->status                = $xenditPayoutLink->status;
            $orderRefund->fast_boat_order_id    = $order->id;
            $orderRefund->type                  = $type;

            $orderRefund->save();

            return $xenditPayoutLink;
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function getOrderRefund($order, $type)
    {
        if ($type == 'fast_boat') $orderRefund = OrderRefund::where('fast_boat_order_id', $order->id)->orderBy('id', 'DESC')->first();

        if ($type == 'private_boat') $orderRefund = OrderRefund::where('private_boat_order_id', $order->id)->orderBy('id', 'DESC')->first();

        if ((isset($orderRefund)) && ($orderRefund->status == 'claimed')) abort(422, 'The refund already success.');

        if ((isset($orderRefund)) && ($orderRefund->status == 'submitted') && ($orderRefund->expired_at >= Carbon::now())) abort(422, 'The refund already requested. Please check on your email.');

        return $orderRefund;
    }

    public function getRefundRequest($order, $type)
    {
        if ($type == 'fast_boat') $refundRequest = RefundRequestPin::where('fast_boat_order_id', $order->id)->orderBy('id', 'DESC')->first();

        if ($type == 'private_boat') $refundRequest = RefundRequestPin::where('private_boat_order_id', $order->id)->orderBy('id', 'DESC')->first();

        if ((isset($refundRequest)) && ($refundRequest->expired_at >= Carbon::now())) return abort(422, 'The pin already requested. PLease try again in 1 minute.');

        return $refundRequest;
    }

    public function createRefundRequestPin($order, $type)
    {
        $newRefundRequest              = new RefundRequestPin();
        $newRefundRequest->pin         = $this->generatePin($order, $type);
        $newRefundRequest->expired_at  = Carbon::now()->addMinutes(1);

        if ($type == 'fast_boat') $newRefundRequest->fast_boat_order_id = $order->id;

        if ($type == 'private_boat') $newRefundRequest->private_boat_order_id = $order->id;

        $newRefundRequest->save();

        return $newRefundRequest;
    }

    protected function generatePin($order, $type)
    {
        do {
            $pin = rand(000000, 999999);

            if ($type == 'fast_boat') $samePin = RefundRequestPin::where('pin', $pin)->where('fast_boat_order_id', $order->id)->count();

            if ($type == 'private_boat') $samePin = RefundRequestPin::where('pin', $pin)->where('private_boat_order_id', $order->id)->count();

        } while (($samePin > 0) || (strlen($pin) != 6));

        return $pin;
    }
}