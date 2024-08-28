<?php

namespace App\Http\Resources\Product\FastBoat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                        => $this->id,
            'fast_boat_id'              => $this->fast_boat_id,
            'is_active'                 => $this->is_active,
            'arrival_time'              => $this->arrival_time,
            'departure_time'            => $this->departure_time,
            'arrival_harbor_name'       => $this->arrivalHarbor->name,
            'departure_hartbor_name'    => $this->departureHarbor->name,

            'fast_boat'     => new FastBoatResource($this->whenLoaded('fastBoat')),
            'variants' => $this->whenLoaded('variants', function(){
                return [
                    'local'   => ScheduleVariantResource::collection($this->variants->where('passenger_type', 'local')),
                    'foreign' => ScheduleVariantResource::collection($this->variants->where('passenger_type', 'foreign'))
                ];
            })
        ];
    }
}
