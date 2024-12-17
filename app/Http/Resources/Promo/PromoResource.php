<?php

namespace App\Http\Resources\Promo;

use Illuminate\Http\Resources\Json\JsonResource;

class PromoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
            'uuid'                      => $this->uuid,
            'code'                      => $this->code,
            'description'               => $this->description,
            'type'                      => $this->type,
            'duration'                  => $this->duration,
            'amount'                    => $this->amount,
            'is_active'                 => $this->is_active,
            'is_all_date'               => $this->is_all_date,
            'is_all_day'                => $this->is_all_day,
            'start_at'                  => $this->start_at,
            'expired_at'                => $this->expired_at,
            'for_start_date'            => $this->for_start_date,
            'for_end_date'              => $this->for_end_date,
            'image'                     => $this->imageUrl('image'),
            'days'                      => $this->whenLoaded('days'),
            'created_at'                => $this->created_at,
            'updated_at'                => $this->updated_at,
        ];
    }
}
