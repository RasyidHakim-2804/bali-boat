<?php

namespace App\Http\Resources\Article;

use App\Http\Resources\Admin\AdminResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminListArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'title'         => $this->title,
            'type'          => $this->type,
            'meta'          => $this->meta,
            'total_view'    => $this->total_view,
            'image'         => $this->imageUrl('image'),
            'author'        => new AdminResource($this->author),
            'created_at'    => date("d F Y", strtotime($this->created_at)),
            'updated_at'    => $this->updated_at,
        ];
    }
}
