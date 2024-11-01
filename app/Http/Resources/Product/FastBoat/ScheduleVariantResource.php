<?php

namespace App\Http\Resources\Product\FastBoat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'fast_boat_schedule_id'  => $this->fast_boat_schedule_id ,
            'passenger_type'         => $this->passenger_type,
            'age_type'               => $this->age_type,
            'price'                  => (int) $this->price,
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at
        ];
    }
}
