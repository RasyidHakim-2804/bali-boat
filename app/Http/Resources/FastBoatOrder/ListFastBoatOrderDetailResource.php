<?php

namespace App\Http\Resources\FastBoatOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class ListFastBoatOrderDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
            'name'                      => $this->name,
            'image'                     => $this->imageUrl('image'),
            'created_at'                => $this->created_at,
        ];
    }
}
