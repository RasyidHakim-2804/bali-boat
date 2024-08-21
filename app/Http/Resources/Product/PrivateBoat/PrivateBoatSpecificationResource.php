<?php

namespace App\Http\Resources\Product\PrivateBoat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivateBoatSpecificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'language'      => $this->language,
            'model'         => $this->model,
            'year'          => $this->year,
            'length'        => $this->length,
            'cabins'        => $this->cabins,
            'heads'         => $this->heads,
            'engine'        => $this->engine,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
