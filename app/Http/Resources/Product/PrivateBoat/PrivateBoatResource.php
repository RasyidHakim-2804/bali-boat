<?php

namespace App\Http\Resources\Product\PrivateBoat;

use App\Http\Resources\Habor\HarborResource;
use App\Http\Resources\Tag\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivateBoatResource extends JsonResource
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
            'is_rented'         => $this->is_rented,
            'active'            => $this->active,
            'pickup_harbor'     => new HarborResource($this->whenLoaded('pickupHarbor')),
            'price'             => $this->defaultVariant->price,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'images'            => PrivateBoatImageResource::collection($this->whenLoaded('images')),

            'tags'           => TagResource::collection($this->whenLoaded('tags')),

            'highlights'     => $this->whenLoaded('highlightTranslations', function(){
                return $this->highlightTranslations->first()->translation;
            }),
            
            'meta'           => $this->whenLoaded('metaTranslations', function(){
                return $this->metaTranslations->first()->translation;
            }),
            
            'specifications' => new PrivateBoatSpecificationResource($this->whenLoaded('specifications', function(){
                return $this->specifications->first();
            })),
            
            'includes'        => $this->whenLoaded('includes'),
            'thumbnail_image' => new PrivateBoatImageResource($this->whenLoaded('thumbnailImage'))
        ];
    }
}
