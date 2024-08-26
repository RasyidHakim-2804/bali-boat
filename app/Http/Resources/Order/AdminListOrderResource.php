<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Address\UserAddressResource;
use App\Http\Resources\User\AdminUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminListOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                            => $this->id,
            'uuid'                          => $this->uuid,
            'address'                       => new UserAddressResource($this->address),
            'grand_total_purchased'         => $this->grand_total_purchased,
            'grand_total_deposit_purchased' => $this->grand_total_deposit_purchased,
            'status'                        => $this->status,
            'is_need_confirmation'          => $this->is_need_confirmation,
            'other_products'                => ($this->details()->count()) - 1,
            'product_thumbnail'             => new ListOrderProductResource($this->details()->first()),
            'activity_date'                 => $this->activity_date,
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
        ];
    }
}
