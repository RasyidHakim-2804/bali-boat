<?php

namespace App\Http\Resources\PrivateBoatOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class ListPrivateBoatOrderDetailResource extends JsonResource
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
