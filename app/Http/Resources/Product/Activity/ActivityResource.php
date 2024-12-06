<?php

namespace App\Http\Resources\Product\Activity;

use App\Http\Resources\Exclude\ListExcludeResource;
use App\Http\Resources\Include\ListIncludeResource;
use App\Http\Resources\Itinerary\ListItineraryResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Tag\ListTagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $price = $this->price? (string) (int) $this->price : null;

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'max_person'        => $this->max_person,
            'is_active'         => $this->is_active,
            'rating'            => (int) $this->rating,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'price'             => $this->whenNotNull($price),

            'highlight' => $this->whenLoaded(
                'translations',
                fn() => $this->translations->where('field','highlight')->first()->translation
            ),

            'description' => $this->whenLoaded(
                'translations',
                fn() => $this->translations->where('field','description')->first()->translation
            ),

            'variants'          => ActivityVariantResource::collection($this->whenLoaded('variants')),
            'location'          => new LocationResource($this->whenLoaded('location')),
            'tags'              => ListTagResource::collection($this->whenLoaded('tags')),
            'includes'          => ListIncludeResource::collection($this->whenLoaded('includes')),
            'excludes'          => ListExcludeResource::collection($this->whenLoaded('excludes')),
            'itineraries'       => ListItineraryResource::collection($this->whenLoaded('itineraries')),
            'thumbnail_image'   => new ActivityImageResource($this->whenLoaded('thumbnailImage')),
            'images'            => ActivityImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
