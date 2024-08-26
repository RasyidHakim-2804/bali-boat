<?php

namespace App\Http\Resources\Product\Activity;

use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Tag\TagResource;
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
            'active'            => $this->active,
            'location'          => new LocationResource($this->whenLoaded('location')),
            'price'             => $this->defaultVariant->price,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,

            'tags'           => TagResource::collection($this->whenLoaded('tags')),

            'highlights'     => $this->whenLoaded('highlightTranslations', function(){
                return $this->highlightTranslations->first()->translation;
            }),
            
            'description'    => $this->whenLoaded('descriptionTranslations', function(){
                return $this->descriptionTranslations->first()->translation;
            }),
            
            'includes'        => ActivityExcludeIncludeResource::collection($this->whenLoaded('includes')),
            'excludes'        => ActivityExcludeIncludeResource::collection($this->whenLoaded('excludes')),
            'itinararies'     => ActivityItinararyResource::collection($this->whenLoaded('itinararies')),
            'thumbnail_image' => new ActivityImageResource($this->whenLoaded('thumbnailImage'))
        ];
    }
}
