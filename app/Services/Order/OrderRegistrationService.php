<?php

namespace App\Services\Order;

use App\Models\OrderForm;

class OrderRegistrationService
{
    public function create($orderId, $registrations)
    {
        foreach ($registrations as $key => $registration) {
            $orderRegistration           = new OrderForm();
            $orderRegistration->order_id = $orderId;

            $orderRegistration->fill($registration);
            
            $orderRegistration->save();
        }
    }
}