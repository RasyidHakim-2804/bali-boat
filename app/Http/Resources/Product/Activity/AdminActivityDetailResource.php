<?php

namespace App\Http\Resources\Product\Activity;

use App\Http\Resources\Exclude\ListExcludeResource;
use App\Http\Resources\Include\ListIncludeResource;
use App\Http\Resources\Itinerary\ListItineraryResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Tag\ListTagResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminActivityDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'price'         => $this->whenLoaded(
                'defaultVariant',
                fn() => (int) $this->defaultVariant->price,
                (int) $this->price
            ),
            'max_person'        => $this->max_person,
            'is_active'         => $this->is_active,
            'rating'            => (int) $this->rating,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'location'          => new LocationResource($this->location),

            'tags'              => ListTagResource::collection($this->tags),

            'highlight'        => ActivityTranslationsResource::collection(
                $this->translations->where('field', 'highlight')
            ),

            'description'       => ActivityTranslationsResource::collection(
                $this->translations->where('field', 'description')
            ),

            'excludes'          => ListExcludeResource::collection($this->excludes),
            'includes'          => ListIncludeResource::collection($this->includes),
            'itineraries'       => ListItineraryResource::collection($this->itineraries),
        ];
    }
}
