<?php

namespace App\Http\Resources\Faq;

use App\Http\Resources\FaqCategory\ListFaqCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminDetailFaqResource extends JsonResource
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

            'category'  => new ListFaqCategoryResource($this->whenLoaded('category')),
            
            'question'  => FaqTranslationResource::collection(
                $this->translations->where('field', 'question')
            ),

            'answer'    => FaqTranslationResource::collection(
                $this->translations->where('field', 'answer')
            ),
        ];
    }
}
