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
            'is_active'         => $this->is_active,
            'rating'            => (int) $this->rating,
            'max_person'        => $this->max_person,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,

            'price'     => $this->when(
                $this->price != null,
                (int) $this->price
            ),
            'is_day_off'        => $this->when(
                $this->relationLoaded('inactiveCalender'),
                fn() => $this->when($this->inactiveCalender->isNotEmpty() != null, true, false)
            ),

            'highlight' => $this->whenLoaded(
                'translations',
                fn() => $this->translations->where('field','highlight')->first()->translation
            ),

            'meta' => $this->whenLoaded(
                'translations',
                fn() => $this->translations->where('field','meta')->first()->translation
            ),

            'specifications' => new PrivateBoatSpecificationResource($this->whenLoaded(
                'specification',
            )),

            'pickup_harbor'     => new HarborResource($this->whenLoaded('pickupHarbor')),
            'variants'          => PrivateBoatVariantResource::collection($this->whenLoaded('variants')),
            'images'            => PrivateBoatImageResource::collection($this->whenLoaded('images')),
            'tags'              => ListTagResource::collection($this->whenLoaded('tags')),
            'includes'          => ListIncludeResource::collection($this->whenLoaded('includes')),
            'thumbnail_image'   => new PrivateBoatImageResource($this->whenLoaded('thumbnailImage'))
        ];
    }
}
