<?php

namespace App\Http\Resources\FastBoatOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class FastBoatOrderDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
            'name'                      => $this->name,
            'fast_boat_id'              => $this->fast_boat_id,
            'schedule_id'               => $this->schedule_id,
            'schedule_variant_id'       => $this->schedule_variant_id,
            'departure_harbor_id'       => $this->departure_harbor_id,
            'arrival_harbor_id'         => $this->arrival_harbor_id,
            'departure_harbor_name'     => $this->departure_harbor_name,
            'arrival_harbor_name'       => $this->arrival_harbor_name,
            'departure_time'            => $this->departure_time,
            'arrival_time'              => $this->arrival_time,
            'name'                      => $this->name,
            'passenger_type'            => $this->passenger_type,
            'age_type'                  => $this->age_type,
            'quantity'                  => $this->quantity,
            'price'                     => $this->price,
            'total_price'               => $this->total_price,
            'image'                     => $this->imageUrl('image'),
            'created_at'                => $this->created_at,
        ];
    }
}
