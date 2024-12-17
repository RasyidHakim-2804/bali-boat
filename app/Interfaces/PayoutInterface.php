<?php

namespace App\Interfaces;

interface PayoutInterface
{
    public function show($id);

    public function payout($requestData, $referralId, $amount, $type);

    public function cancel($id);
}