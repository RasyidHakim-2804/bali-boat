<?php

namespace App\Services\Logs;

use App\Models\OrderPaymentLog;

class OrderPaymentLogService
{
    public function store($orderId, $paymentVendor, $status, $type)
    {
        $paymentLog                     = new OrderPaymentLog();
        $paymentLog->order_id           = $orderId;
        $paymentLog->type               = $type;
        $paymentLog->payment_vendor     = $paymentVendor;
        $paymentLog->status             = $status;

        return $paymentLog->save();
    }
}