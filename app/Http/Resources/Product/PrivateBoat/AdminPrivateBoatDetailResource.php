<?php

namespace App\Http\Resources\Product\PrivateBoat;

use App\Http\Resources\Habor\HarborResource;
use App\Http\Resources\Include\ListIncludeResource;
use App\Http\Resources\Tag\ListTagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminPrivateBoatDetailResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray( $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'price'         => $this->whenLoaded(
                'defaultVariant',
                fn() => $this->defaultVariant->price,
                $this->price
            ),
            'is_active'         => $this->is_active,
            'max_person'        => $this->max_person,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'pickup_harbor'     => new HarborResource($this->pickupHarbor),

            'tags'           => ListTagResource::collection($this->tags),

            'highlight'     => PrivateBoatTranslationResource::collection(
                $this->translations->where('field', 'highlight')
            ),

            'meta'           => PrivateBoatTranslationResource::collection(
                $this->translations->where('field', 'meta')
            ),

            'specifications' => new PrivateBoatSpecificationResource($this->specification),

            'includes'       => ListIncludeResource::collection($this->includes),

        ];
    }
}
