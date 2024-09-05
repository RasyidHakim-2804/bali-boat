<?php

namespace App\Http\Resources\FastBoatOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class FastBoatOrderResource extends JsonResource
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
            'payment_method'                => ($this->payment) ? str_replace('_', ' ', $this->payment->payment_method) . ' - ' . $this->payment->payment_channel : null,
            'refund_status'                 => $this->refunds()?->orderBy('id', 'DESC')->first()?->status ?? null,
            'status'                        => $this->status,
            'activity_date'                 => $this->activity_date,
            'return_date'                   => $this->return_date,
            'details'                       => FastBoatOrderDetailResource::collection($this->details),
            'forms'                         => FastBoatOrderFormResource::collection($this->forms),
            // 'refund'                        => OrderRefundResource::collection($this->refunds),
            // 'review'                    => new OrderReviewResource($this->review),
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
        ];
    }
}
