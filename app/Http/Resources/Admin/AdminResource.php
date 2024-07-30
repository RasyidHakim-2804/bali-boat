<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'uuid'             => $this->uuid,
            'first_name'       => $this->first_name,
            'last_name'        => $this->last_name,
            'email'            => $this->email,
            'profile_picture'  => $this->imageUrl('profile_picture'),
            'is_active'        => $this->is_active,
            'permissions'      => $this->permissions,
            'deleted_at'       => $this->deleted_at,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
