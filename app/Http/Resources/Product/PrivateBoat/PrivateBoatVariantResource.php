<?php

namespace App\Http\Resources\Product\PrivateBoat;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivateBoatVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'private_boat_id'   => $this->private_boat_id,
            'is_default'        => $this->is_default,
            'price'             => (int) $this->price,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ];
    }
}
