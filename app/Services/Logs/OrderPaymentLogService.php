<?php

namespace App\Services\Logs;

use App\Models\OrderPaymentLog;

class OrderPaymentLogService
{
    public function store($orderId, $paymentVendor, $status)
    {
        $paymentLog                     = new OrderPaymentLog();
        $paymentLog->fast_boat_order_id = $orderId;
        $paymentLog->payment_vendor     = $paymentVendor;
        $paymentLog->status             = $status;

        return $paymentLog->save();
    }
}