<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\StoreArticleCommentRequest;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class ArticleCommentController extends Controller
{
    use JsonResponsesTrait;

    public function store(StoreArticleCommentRequest $request, Article $article)
    {
        $comment             = new ArticleComment();
        $comment->article_id = $article->id;

        $comment->fill($request->validated());

        $comment->save();

        return $this->successStore();
    }
}
