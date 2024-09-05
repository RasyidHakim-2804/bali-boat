<?php

namespace App\Services\Order;

use App\Models\OrderRefund;
use App\Services\Payment\PaymentPayoutService;
use Illuminate\Support\Facades\Mail;

class OrderRefundService
{
    public function refund($order, $type)
    {
        $user   = $order->user;
        $email  = $user->email;

        try {
            $xenditPayoutLink = (new PaymentPayoutService)->prepare()->payout($email, $order->uuid, (float)$order->grand_total_purchased, 'refund');

            $orderRefund                        = new OrderRefund();
            $orderRefund->xendit_payout_link_id = $xenditPayoutLink->id;
            $orderRefund->status                = $xenditPayoutLink->status;
            $orderRefund->fast_boat_order_id    = $order->id;
            $orderRefund->type                  = $type;

            $orderRefund->save();

            return $orderRefund;
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }
}