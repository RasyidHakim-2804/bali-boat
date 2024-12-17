<?php

namespace App\Http\Resources\FastBoatOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class FastBoatOrderFormResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'order_id'       => $this->order_id,
            'schedule_id'    => $this->schedule_id,
            'variant_id'     => $this->variant_id,
            'name'           => $this->name,
            'nationality'    => $this->nationality,
            'passenger_type' => $this->passenger_type,
            'age_type'       => $this->age_type,
            'is_itas_holder' => $this->is_itas_holder,
            'price'          => $this->scheduleVariant->price,
            'destination'    => $this->schedule->departureHarbor->name . ' - ' . $this->schedule->arrivalHarbor->name,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->created_at,
        ];
    }
}
