<?php

namespace App\Services\FastBoatOrder;

use App\Models\FastBoatOrderForm;

class FastBoatOrderRegistrationService
{
    public function create($orderId, $registrations)
    {
        foreach ($registrations as $key => $registration) {
            $orderRegistration              = new FastBoatOrderForm();
            $orderRegistration->order_id    = $orderId;
            $orderRegistration->schedule_id = $registration['product_id'];

            if ($registration['nationality'] == 'indonesia')  $orderRegistration->is_local = 1;

            $orderRegistration->fill($registration);
            
            $orderRegistration->save();
        }
    }
}