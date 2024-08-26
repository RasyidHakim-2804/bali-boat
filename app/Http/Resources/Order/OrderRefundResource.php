<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderRefundResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'status'                => $this->status,
            'bank_code'             => $this->xenditPayoutLink?->bank_code,
            'account_holder_name'   => $this->xenditPayoutLink?->account_holder_name,
            'claimed_at'            => $this->xenditPayoutLink?->claimed_at,
            'expired_at'            => $this->xenditPayoutLink?->expired_at,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->created_at,
            'payout_link'           => $this->xenditPayoutLink,
        ];
    }
}
