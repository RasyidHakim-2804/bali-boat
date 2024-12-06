<?php

namespace App\Http\Resources\Product\Activity;

use App\Http\Resources\Exclude\ListExcludeResource;
use App\Http\Resources\Include\ListIncludeResource;
use App\Http\Resources\Itinerary\ListItineraryResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Tag\ListTagResource;
use App\Models\ActivityTranslation;
use App\Models\ActivityVariant;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminActivityDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $resource = [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'max_person'        => $this->max_person,
            'is_active'         => $this->is_active,
            'rating'            => (int) $this->rating,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'thumbnail_image'   => new ActivityImageResource($this->whenLoaded('thumbnailImage')),
            'location'          => new LocationResource($this->location),
            'variants'          => $this->whenLoaded('variants', function(){
                $result = [];

                foreach (ActivityVariant::PASSENGER_TYPE as $field) {
                    $result[$field] = ActivityVariantResource::collection($this->variants->where('passenger_type', $field));
                }
                return $result;
            }),

            'tags'              => ListTagResource::collection($this->tags),

            'excludes'          => ListExcludeResource::collection($this->excludes),
            'includes'          => ListIncludeResource::collection($this->includes),
            'itineraries'       => ListItineraryResource::collection($this->itineraries),
        ];

        return $this->addTranslation($resource);
    }

    private function addTranslation(array $resource)
    {
        foreach (ActivityTranslation::FIELD as $field) {
            $resource[$field] = ActivityTranslationsResource::collection(
                $this->translations->where('field', $field)
            );
        }

        return $resource;
    }
}
