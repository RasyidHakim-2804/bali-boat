<?php

namespace App\Http\Resources\Article;

use App\Http\Resources\Admin\AdminResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ListArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'title'         => $this->titleTranslations[0]?->translation,
            'type'          => $this->type,
            'tag'           => $this->tagTranslations[0]?->translation,
            'meta'          => $this->metaTranslations[0]?->translation,
            'total_view'    => $this->total_view,
            'image'         => $this->imageUrl('image'),
            'author'        => new AdminResource($this->author),
            'created_at'    => date("d F Y", strtotime($this->created_at)),
            'updated_at'    => $this->updated_at,
        ];
    }
}
