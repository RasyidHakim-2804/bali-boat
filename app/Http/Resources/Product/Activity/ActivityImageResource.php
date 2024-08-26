<?php

namespace App\Http\Resources\Product\Activity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray( $request): array
    {
        return [
            'id'            => $this->id,
            'activity_id'       => $this->activity_id,
            'image'         => $this->imageUrl('image'),
            'position'      => $this->position,
            'updated_at'    => $this->updated_at,
            'created_at'    => $this->created_at,
        ];
    }
}
