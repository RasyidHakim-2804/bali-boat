<?php

namespace App\Http\Resources\ActivityOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityOrderResource extends JsonResource
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
            'payment'                       => $this->payment,
            'refund_status'                 => $this->refunds()?->orderBy('id', 'DESC')->first()?->status ?? null,
            'status'                        => $this->status,
            'activity_date'                 => $this->activity_date,
            'details'                       => ActivityOrderDetailResource::collection($this->details),
            'forms'                         => ActivityOrderFormResource::collection($this->forms),
            // 'refund'                        => OrderRefundResource::collection($this->refunds),
            // 'review'                    => new OrderReviewResource($this->review),
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
        ];
    }
}
