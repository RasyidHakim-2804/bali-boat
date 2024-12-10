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
            'type'          => $this->type,
            'image'         => $this->imageUrl('image'),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,

            'title'         => $this->translations
                                ->where('field', 'title')
                                ->pluck('translation')
                                ->first(),

            'description'   => $this->translations
                                ->where('field', 'description')
                                ->pluck('translation')
                                ->first(),

            $this->mergeWhen(
                isset($this->pivot),
                fn() => ['position'  => $this->pivot->position]
            ),
        ];
    }
}
