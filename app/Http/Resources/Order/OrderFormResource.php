<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderFormResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
            'order_id'                  => $this->order_id,
            'product_id'                => $this->product_id,
            'variant_id'                => $this->variant_id,
            'name'                      => $this->name,
            'date_of_birth'             => $this->date_of_birth,
            'gender'                    => $this->gender,
            'nationality'               => $this->nationality,
            'email'                     => $this->email,
            'phone'                     => $this->phone,
            'pickup_address'            => $this->pickup_address,
            'diet_restriction'          => $this->diet_restriction,
            'shoes_size'                => $this->shoes_size,
            'certification_level'       => $this->certification_level,
            'diving_association_number' => $this->diving_association_number,
            'last_dive'                 => $this->last_dive,
            'is_pic'                    => $this->is_pic,
            'created_at'                => $this->created_at,
            'updated_at'                => $this->created_at,
        ];
    }
}
