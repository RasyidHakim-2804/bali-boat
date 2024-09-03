<?php

namespace App\Http\Resources\Tag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminDetailTagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'type'          => $this->type,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'name'   => TagTranslationResource::collection(
                $this->translations->where('field', 'name')
            ),
        ];
    }
}
