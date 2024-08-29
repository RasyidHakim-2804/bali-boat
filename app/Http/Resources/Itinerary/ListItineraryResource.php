<?php

namespace App\Http\Resources\Itinerary;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListItineraryResource extends JsonResource
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
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'image'         => $this->imageUrl('image'),
            'title'         => $this->translations->first()->translation,
            $this->mergeWhen(
                isset($this->pivot),
                fn() => ['position'  => $this->pivot->position]
            ),
        ];
    }
}
