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
            $orderRegistration->schedule_id = $registration['product_id'];

            if ($registration['nationality'] == 'indonesia')  $orderRegistration->is_local = 1;

            $orderRegistration->fill($registration);
            
            $orderRegistration->save();
        }
    }
}