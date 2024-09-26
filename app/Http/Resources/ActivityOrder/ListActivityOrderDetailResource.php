<?php

namespace App\Http\Resources\ActivityOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class ListActivityOrderDetailResource extends JsonResource
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
