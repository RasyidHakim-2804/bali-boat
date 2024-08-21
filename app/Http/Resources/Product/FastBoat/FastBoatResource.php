<?php

namespace App\Http\Resources\Product\FastBoat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FastBoatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'image'             => $this->imageUrl('image'),
            'rating'              => $this->rating,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,

            'schedules'     => ScheduleResource::collection($this->whenLoaded('schedules'))
        ];;
    }
}
