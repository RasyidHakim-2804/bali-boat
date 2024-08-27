<?php

namespace App\Http\Resources\Exclude;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminDetailExcludeResource extends JsonResource
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
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'description'   => ExcludeTranslationResource::collection(
                $this->translations->where('field', 'description')
            ),
        ];
    }
}
