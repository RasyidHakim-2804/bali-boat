<?php

namespace App\Http\Resources\Product\PrivateBoat;

use App\Http\Resources\Habor\HarborResource;
use App\Http\Resources\Tag\TagResource;
use App\Traits\ResourceHasTranslationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminPrivateBoatResource extends JsonResource
{
    use ResourceHasTranslationTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
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

            'tags'           => TagResource::collection($this->whenLoaded('tags')),
            'highlights'     => $this->separateByLanguage(PrivateBoatTranslationResource::class, 'highlightTranslations', false),
            'meta'           => $this->separateByLanguage(PrivateBoatTranslationResource::class, 'metaTranslations', false),
            'specifications' => $this->separateByLanguage(PrivateBoatSpecificationResource::class, 'specifications', false),
            'includes'       => $this->separateByLanguage(PrivateBoatIncludeResource::class, 'includes')

        ];
    }
}
