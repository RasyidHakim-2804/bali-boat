<?php

namespace App\Http\Resources\FastBoatOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminListFastBoatOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                            => $this->id,
            'uuid'                          => $this->uuid,
            'pic_name'                      => $this->pic_name,
            'pic_email'                     => $this->pic_email,
            'pic_phone_number'              => $this->pic_phone_number,
            'grand_total_purchased'         => $this->grand_total_purchased,
            'status'                        => $this->status,
            'other_products'                => ($this->details()->count()) - 1,
            'product_thumbnail'             => new ListFastBoatOrderDetailResource($this->details()->first()),
            'activity_date'                 => $this->activity_date,
            'return_date'                   => $this->return_date,
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
        ];
    }
}
