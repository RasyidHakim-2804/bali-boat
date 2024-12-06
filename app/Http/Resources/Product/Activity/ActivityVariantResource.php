<?php

namespace App\Http\Resources\Product\Activity;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityVariantResource extends JsonResource
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
            'activity_id'       => $this->activity_id,
            'is_default'        => $this->is_default,
            'passenger_type'    => $this->passenger_type,
            'price'             => (string) (int) $this->price,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ];
    }
}
