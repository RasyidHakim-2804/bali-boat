<?php

namespace App\Http\Resources\Product\PrivateBoat;

use App\Http\Resources\Habor\HarborResource;
use App\Http\Resources\Include\ListIncludeResource;
use App\Http\Resources\Tag\ListTagResource;
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
            'is_active'         => $this->is_active,
            'pickup_harbor'     => new HarborResource($this->whenLoaded('pickupHarbor')),
            'price'             => $this->defaultVariant->price,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'images'            => PrivateBoatImageResource::collection($this->whenLoaded('images')),

            'tags'           => ListTagResource::collection($this->whenLoaded('tags')),

            'highlight' => $this->whenLoaded(
                'translations',
                fn() => $this->translations->where('field','highlight')->first()->translation
            ),
            
            'meta' => $this->whenLoaded(
                'translations',
                fn() => $this->translations->where('field','meta')->first()->translation
            ),
            
            'specifications' => new PrivateBoatSpecificationResource($this->whenLoaded(
                'specifications',
                fn() => $this->specifications->first()
            )),
            
            'includes'        => ListIncludeResource::collection($this->whenLoaded('includes')),
            'thumbnail_image' => new PrivateBoatImageResource($this->whenLoaded('thumbnailImage'))
        ];
    }
}
