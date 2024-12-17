<?php

namespace App\Http\Resources\Faq;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListFaqResource extends JsonResource
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
            'is_top'        => $this->is_top,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,

            'question'      => $this->translations
                                ->where('field', 'question')
                                ->first()
                                ->translation,

            'answer'        => $this->translations
                                ->where('field', 'answer')
                                ->first()
                                ->translation,
        ];
    }
}
