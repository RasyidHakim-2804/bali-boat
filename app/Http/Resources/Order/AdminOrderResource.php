<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                            => $this->id,
            'uuid'                          => $this->uuid,
            'user'                          => $this->user,
            'total_purchased'               => $this->total_purchased,
            'total_additional_purchased'    => $this->total_additional_purchased,
            'promo_amount'                  => $this->promo_amount,
            'grand_total_purchased'         => $this->grand_total_purchased,
            'grand_total_deposit_purchased' => $this->grand_total_deposit_purchased,
            'payment_url'                   => (isset($this->payment)) ? $this->payment->url : null,
            'payment_status'                => ($this->payment) ? $this->payment->status : 'not in system',
            'payment'                       => $this->payment,
            'refund_status'                 => $this->refunds()?->orderBy('id', 'DESC')->first()?->status ?? null,
            'status'                        => $this->status,
            'activity_date'                 => $this->activity_date,
            'pickup_address'                => $this->pickup_address,
            'is_need_confirmation'          => $this->is_need_confirmation,
            'details'                       => OrderProductResource::collection($this->details),
            'additionals'                   => OrderAdditionalResource::collection($this->additionals),
            'forms'                         => OrderFormResource::collection($this->forms),
            'emergency_contact'             => $this->emergencyContact,
            'refund'                        => OrderRefundResource::collection($this->refunds),
            'decline_reason'                => $this->decline?->reason,
            // 'review'                    => new OrderReviewResource($this->review),
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
        ];
    }
}
