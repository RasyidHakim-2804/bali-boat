<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderAdditionalResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
            'product_id'                => $this->product_id,
            'variant_id'                => $this->variant_id,
            'additional_id'             => $this->additional_id,
            'name'                      => $this->name,
            'description'               => $this->description,
            'quantity'                  => $this->quantity,
            'price'                     => $this->price,
            'total_price'               => $this->total_price,
            'created_at'                => $this->created_at,
        ];
    }
}
