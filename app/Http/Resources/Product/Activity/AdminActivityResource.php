<?php

namespace App\Http\Resources\Product\Activity;

use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Tag\TagResource;
use App\Traits\ResourceHasTranslationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminActivityResource extends JsonResource
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
            'max_person'        => $this->max_person,
            'active'            => $this->active,
            'location'          => new LocationResource($this->whenLoaded('location')),
            'price'             => $this->defaultVariant->price,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,

            'tags'              => TagResource::collection($this->whenLoaded('tags')),
            'highlights'        => $this->separateByLanguage(ActivityTranslationsResource::class, 'highlightTranslations', false),
            'description'       => $this->separateByLanguage(ActivityTranslationsResource::class, 'descriptionTranslations', false),
            'excludes'          => $this->separateByLanguage(ActivityExcludeIncludeResource::class, 'excludes'),
            'includes'          => $this->separateByLanguage(ActivityExcludeIncludeResource::class, 'includes'),
            'itineraries'       => $this->separateByLanguage(ActivityItinararyResource::class, 'itinararies'),
        ];
    }
}
