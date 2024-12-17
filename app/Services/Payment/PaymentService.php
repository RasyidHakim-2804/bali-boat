<?php

namespace App\Services\Payment;

use App\Services\Xendit\XenditInvoiceService;

class PaymentService
{
    public function prepare($integrationName = null)
    {
        if (!$integrationName) $integrationName = env('PAYMENT_VENDOR');

        if ($integrationName == 'xendit_invoice') return new XenditInvoiceService();

        abort(500, 'No payment service found.');
    }
}