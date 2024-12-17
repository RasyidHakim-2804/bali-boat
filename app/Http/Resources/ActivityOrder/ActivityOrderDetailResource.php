<?php

namespace App\Http\Resources\ActivityOrder;

use App\Http\Resources\Tag\ListTagResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityOrderDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'order_id'      => $this->order_id,
            'product_id'    => $this->product_id,
            'variant_id'    => $this->variant_id,
            'location_id'   => $this->location_id,
            'location_name' => $this->location_name,
            'product_name'  => $this->product_name,
            'variant_name'  => $this->variant_name,
            'passenger_type'=> $this->passenger_type,
            'quantity'      => $this->quantity,
            'price'         => $this->price,
            'total_price'   => $this->total_price,
            'image'         => $this->image,
            'product_tags'  => ListTagResource::collection($this->product->tags),
            'created_at'    => $this->created_at,
        ];
    }
}
