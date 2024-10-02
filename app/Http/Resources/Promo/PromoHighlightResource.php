<?php

namespace App\Http\Resources\Promo;

use Illuminate\Http\Resources\Json\JsonResource;

class PromoHighlightResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
            'promo'                     => new PromoResource($this->promo),
            'created_at'                => $this->created_at,
        ];
    }
}
