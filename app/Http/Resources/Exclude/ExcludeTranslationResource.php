<?php

namespace App\Http\Resources\Exclude;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExcludeTranslationResource extends JsonResource
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
            'language'      => $this->language,
            'include_id'    => $this->include_id,
            'field'         => $this->field,
            'translation'   => $this->translation,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
