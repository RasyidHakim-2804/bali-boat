<?php

namespace App\Http\Resources\Article;

use Illuminate\Http\Resources\Json\JsonResource;

class ListArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'title'         => $this->title,
            'type'          => $this->type,
            'tag'           => $this->tag,
            'meta'          => $this->metaTranslations[0]->translation,
            'total_view'    => $this->total_view,
            'image'         => $this->imageUrl('image'),
            'created_at'    => date("d F Y", strtotime($this->created_at)),
            'updated_at'    => $this->updated_at,
        ];
    }
}
