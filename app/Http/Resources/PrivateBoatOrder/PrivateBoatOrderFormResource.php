<?php

namespace App\Http\Resources\PrivateBoatOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivateBoatOrderFormResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'order_id'       => $this->order_id,
            'schedule_id'    => $this->product_id,
            'variant_id'     => $this->variant_id,
            'name'           => $this->name,
            'nationality'    => $this->date_of_birth,
            'passenger_type' => $this->passenger_type,
            'age_type'       => $this->age_type,
            'is_itas_holder' => $this->is_itas_holder,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->created_at,
        ];
    }
}
