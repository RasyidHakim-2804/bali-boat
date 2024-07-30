<?php

namespace App\Http\Resources\ArticleCategory;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'slug'  => $this->slug,
            'name'  => $this->name,
        ];
    }
}
