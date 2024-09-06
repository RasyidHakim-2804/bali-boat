<?php

namespace App\Http\Resources\FastBoatOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class ListFastBoatOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
            'uuid'                      => $this->uuid,
            'pic_name'                  => $this->pic_name,
            'pic_email'                 => $this->pic_email,
            'pic_phone_number'          => $this->pic_phone_number,
            'grand_total_purchased'     => $this->grand_total_purchased,
            'status'                    => $this->status,
            'destination'               => $this->details->first()->departure_harbor_name . ' - ' . $this->details->first()->arrival_harbor_name,
            'activity_date'             => $this->activity_date,
            'activity_time'             => $this->details->first()->departure_time,
            'return_date'               => $this->return_date,
            'type'                      => $this->return_date ? 'round_trip' : 'one_way',
            'totalForms'                => $this->forms_count,
            'total+passenger'           => $this->return_date ? $this->forms_count/2 : $this->forms_count,
            'created_at'                => $this->created_at,
            'updated_at'                => $this->updated_at,
        ];
    }
}
