<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminPermissionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'feature_id'    => $this->feature_id,
            'feature_name'  => $this->feature->name,
            'type'          => $this->type,
        ];
    }
}
