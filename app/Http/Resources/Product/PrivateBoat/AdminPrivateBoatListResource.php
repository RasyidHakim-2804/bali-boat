<?php

namespace App\Http\Resources\Product\PrivateBoat;

use App\Http\Resources\Habor\HarborResource;
use App\Http\Resources\Tag\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminPrivateBoatListResource extends JsonResource
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
            'price'             => $this->whenLoaded(
                'defaultVariant',
                fn() => (string) (int) $this->defaultVariant->price,
                (string) (int) $this->price
            ),
            'slug'              => $this->slug,
            'is_active'         => $this->is_active,
            'rating'            => (int) $this->rating,
            'max_person'        => $this->max_person,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
