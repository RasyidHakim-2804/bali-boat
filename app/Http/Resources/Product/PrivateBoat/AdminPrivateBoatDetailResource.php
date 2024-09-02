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
            'is_active'         => $this->is_active,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'pickup_harbor'     => new HarborResource($this->pickupHarbor),
            'default_variant'   => new PrivateBoatVariantResource($this->defaultVariant),
            'variants'          => PrivateBoatVariantResource::collection($this->variants),

            'tags'           => ListTagResource::collection($this->tags),

            'highlight'     => PrivateBoatTranslationResource::collection(
                $this->translations->where('field', 'highlight')
            ),

            'meta'           => PrivateBoatTranslationResource::collection(
                $this->translations->where('field', 'meta')
            ),

            'specifications' => PrivateBoatSpecificationResource::collection($this->specification),

            'includes'       => ListIncludeResource::collection($this->includes),

        ];
    }
}
