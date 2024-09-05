<?php

namespace App\Services\PrivateBoatOrder;

use App\Models\PrivateBoatOrderForm;

class PrivateBoatOrderRegistrationService
{
    public function create($orderId, $registrations)
    {
        foreach ($registrations as $key => $registration) {
            $orderRegistration              = new PrivateBoatOrderForm();
            $orderRegistration->order_id    = $orderId;
            $orderRegistration->product_id  = $registration['product_id'];

            $orderRegistration->fill($registration);
            
            $orderRegistration->save();
        }
    }
}