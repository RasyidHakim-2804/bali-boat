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
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'max_person'        => $this->max_person,
            'is_active'         => $this->is_active,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            
            
            'highlight' => $this->whenLoaded(
                'translations',
                fn() => $this->translations->where('field','highlight')->first()->translation
            ),
            
            'description' => $this->whenLoaded(
                'translations',
                fn() => $this->translations->where('field','description')->first()->translation
            ),
            
            'thumbnail_variant' => new ActivityVariantResource($this->whenLoaded('thumbnailVariant')),
            'variants'          => ActivityVariantResource::collection($this->whenLoaded('variants')),
            'default_variant'   => new ActivityVariantResource($this->whenLoaded('defaultVariant')),
            'location'          => new LocationResource($this->whenLoaded('location')),
            'tags'              => ListTagResource::collection($this->whenLoaded('tags')),
            'includes'          => ListIncludeResource::collection($this->whenLoaded('includes')),
            'excludes'          => ListExcludeResource::collection($this->whenLoaded('excludes')),
            'itinararies'       => ListItineraryResource::collection($this->whenLoaded('itinararies')),
            'thumbnail_image'   => new ActivityImageResource($this->whenLoaded('thumbnailImage')),
            'images'            => ActivityImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
