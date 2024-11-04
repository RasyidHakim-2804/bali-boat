<?php

namespace App\Http\Resources\PrivateBoatOrder;

use App\Http\Resources\Tag\ListTagResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivateBoatOrderDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
            'product_id'                => $this->product_id,
            'variant_id'                => $this->variant_id,
            'name'                      => $this->name,
            'quantity'                  => $this->quantity,
            'price'                     => $this->price,
            'total_price'               => $this->total_price,
            'image'                     => $this->imageUrl('image'),
            'product_tags'              => ListTagResource::collection($this->product->tags),
            'created_at'                => $this->created_at,
        ];
    }
}
