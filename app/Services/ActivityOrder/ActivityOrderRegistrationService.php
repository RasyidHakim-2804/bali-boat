<?php

namespace App\Services\ActivityOrder;

use App\Models\ActivityOrderForm;

class ActivityOrderRegistrationService
{
    public function create($orderId, $registrations)
    {
        foreach ($registrations as $key => $registration) {
            $orderRegistration              = new ActivityOrderForm();
            $orderRegistration->order_id    = $orderId;
            $orderRegistration->product_id  = $registration['product_id'];

            $orderRegistration->fill($registration);
            
            $orderRegistration->save();
        }
    }
}