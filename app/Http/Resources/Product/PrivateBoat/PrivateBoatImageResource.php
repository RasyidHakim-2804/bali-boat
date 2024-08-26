<?php

namespace App\Http\Resources\Product\PrivateBoat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivateBoatImageResource extends JsonResource
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
            'private_boat_id'     => $this->private_boat_id,
            'image'         => $this->imageUrl('image'),
            'position'      => $this->position,
            'updated_at'    => $this->updated_at,
            'created_at'    => $this->created_at,
        ];
    }
}
