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
            'is_new_user_promo'         => $this->is_new_user_promo,
            'start_new_user_promo_at'   => $this->start_new_user_promo_at,
            'start_at'                  => $this->start_at,
            'expired_at'                => $this->expired_at,
            'created_at'                => $this->created_at,
            'updated_at'                => $this->updated_at,
        ];
    }
}
