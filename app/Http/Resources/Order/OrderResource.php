<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\OrderReview\OrderReviewResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                            => $this->id,
            'uuid'                          => $this->uuid,
            'total_purchased'               => $this->total_purchased,
            'grand_total_purchased'         => $this->grand_total_purchased,
            'grand_total_deposit_purchased' => $this->grand_total_deposit_purchased,
            'payment_url'                   => (isset($this->payment)) ? $this->payment->url : null,
            'payment_status'                => ($this->payment) ? $this->payment->status : 'not in system',
            'refund_status'                 => $this->refunds()?->orderBy('id', 'DESC')->first()?->status ?? null,
            'status'                        => $this->status,
            'decline_reason'                => $this->decline_reason,
            'activity_date'                 => $this->activity_date,
            'pickup_address'                => $this->pickup_address,
            'details'                       => OrderProductResource::collection($this->details),
            'additionals'                   => OrderAdditionalResource::collection($this->additionals),
            'forms'                         => OrderFormResource::collection($this->forms),
            'emergency_contact'             => $this->emergencyContact,
            'is_need_confirmation'          => $this->is_need_confirmation,
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
        ];
    }
}
