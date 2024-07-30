<?php

namespace App\Http\Resources\Article;

use App\Http\Resources\User\UserProfileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleCommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'comment'       => $this->comment,
            'created_at'    => date("d F Y", strtotime($this->created_at)),
        ];
    }
}
