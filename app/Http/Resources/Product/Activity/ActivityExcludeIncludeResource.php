<?php

namespace App\Http\Resources\Product\Activity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityExcludeIncludeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'value'         => $this->value,
            'position'      => $this->position,
            'activity_id'       => $this->activity_id,
            'language'      => $this->language,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
