<?php

namespace App\Interfaces;

interface PaymentInterface
{
    public function list();

    public function show($id);

    public function pay($data, $type);

    public function expire($id);
}