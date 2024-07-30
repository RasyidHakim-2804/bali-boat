<?php

namespace App\Http\Resources\Article;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'image'     => 'storage/upload/' . $this->imagePath() . '/' . $this->image,
        ];
    }
}
