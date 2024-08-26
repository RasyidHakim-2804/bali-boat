<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
            'name'                      => $this->name,
            'product_id'                => $this->product_id,
            'variant_id'                => $this->variant_id,
            'name'                      => $this->name,
            'description'               => $this->description,
            'location'                  => $this->location?->name,
            'quantity'                  => $this->quantity,
            'price'                     => $this->price,
            'usd_price'                 => $this->usd_price,
            'eur_price'                 => $this->eur_price,
            'jpy_price'                 => $this->jpy_price,
            'cny_price'                 => $this->cny_price,
            'total_price'               => $this->total_price,
            'total_usd_price'           => $this->total_usd_price,
            'total_eur_price'           => $this->total_eur_price,
            'total_jpy_price'           => $this->total_jpy_price,
            'total_cny_price'           => $this->total_cny_price,
            'image'                     => $this->imageUrl('image'),
            'created_at'                => $this->created_at,
        ];
    }
}
