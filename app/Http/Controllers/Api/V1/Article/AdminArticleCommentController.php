<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\Article\ListArticleResource;
use App\Models\Admin;
use App\Models\ArticleComment;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminArticleCommentController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        return ListArticleResource::collection(ArticleComment::latest()->paginate(25));
    }

    public function destroy(ArticleComment $comment)
    {
        $comment->delete();

        return $this->successStore();
    }
}
