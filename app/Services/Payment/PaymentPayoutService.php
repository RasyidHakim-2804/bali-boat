<?php

namespace App\Services\Payment;

use App\Services\Xendit\XenditPayoutLinkService;
use App\Services\Xendit\XenditPayoutService;

class PaymentPayoutService
{
    public function prepare($integrationName = null)
    {
        if (!$integrationName) $integrationName = env('PAYMENT_PAYOUT_VENDOR');

        if ($integrationName == 'xendit_payout') return new XenditPayoutService();

        if ($integrationName == 'xendit_payout_link') return new XenditPayoutLinkService();

        abort(500, 'No payment refund service found.');
    }
}