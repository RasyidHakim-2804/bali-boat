<?php

namespace App\Http\Resources\ActivityOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class ListActivityOrderDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'product_name'  => $this->product_name,
            'variant_name'  => $this->variant_name,
            'image'         => $this->imageUrl('image'),
            'created_at'    => $this->created_at,
        ];
    }
}
