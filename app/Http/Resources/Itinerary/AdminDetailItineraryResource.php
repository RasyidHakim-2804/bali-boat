<?php

namespace App\Http\Resources\Itinerary;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminDetailItineraryResource extends JsonResource
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
            'type'          => $this->type,
            'image'         => $this->imageUrl('image'),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,

            'description'   => ItineraryTranslationResource::collection(
                $this->translations->where('field', 'description')
            ),

            'title'         => ItineraryTranslationResource::collection(
                $this->translations->where('field', 'title')
            ),
        ];
    }
}
