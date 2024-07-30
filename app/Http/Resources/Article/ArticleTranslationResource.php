<?php

namespace App\Http\Resources\Article;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleTranslationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'language'      => $this->language,
            'translation'   => $this->translation,
        ];
    }
}
