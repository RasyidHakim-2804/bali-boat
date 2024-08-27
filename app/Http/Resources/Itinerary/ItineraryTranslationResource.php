<?php

namespace App\Http\Resources\Itinerary;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryTranslationResource extends JsonResource
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
            'include_id'    => $this->include_id,
            'field'         => $this->field,
            'language'      => $this->language,
            'translation'   => $this->translation,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
