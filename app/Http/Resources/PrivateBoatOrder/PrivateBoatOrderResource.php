<?php

namespace App\Http\Resources\PrivateBoatOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivateBoatOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                            => $this->id,
            'uuid'                          => $this->uuid,
            'user'                          => $this->user,
            'pic_name'                      => $this->pic_name,
            'pic_email'                     => $this->pic_email,
            'pic_phone_number'              => $this->pic_phone_number,
            'total_purchased'               => $this->total_purchased,
            'promo_amount'                  => $this->promo_amount,
            'grand_total_purchased'         => $this->grand_total_purchased,
            'payment_url'                   => (isset($this->payment)) ? $this->payment->url : null,
            'payment_status'                => ($this->payment) ? $this->payment->status : 'not in system',
            'refund_status'                 => $this->refunds()?->orderBy('id', 'DESC')->first()?->status ?? null,
            'status'                        => $this->status,
            'activity_date'                 => $this->activity_date,
            'details'                       => PrivateBoatOrderDetailResource::collection($this->details),
            'forms'                         => PrivateBoatOrderFormResource::collection($this->forms),
            // 'refund'                        => OrderRefundResource::collection($this->refunds),
            // 'review'                    => new OrderReviewResource($this->review),
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
        ];
    }
}
