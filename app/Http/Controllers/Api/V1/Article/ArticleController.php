<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\Article\ArticleResource;
use App\Http\Resources\Article\ListArticleResource;
use App\Models\Article;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        Article::where('type', null)->update(['type' => 'travel']);
        return ListArticleResource::collection(Article::latest()->with(['metaTranslations' => function($translation) {
            $translation->where('field', 'meta')->where('language', config('app.locale'));
        }])->paginate(15));
    }

    public function show(Article $article)
    {
        $article->total_view = $article->total_view + 1;

        $article->save();

        $article = new ArticleResource($article);
    
        return $this->successGet($article);
    }

    public function top()
    {
        $type = request()->query('type');

        if (!$type) $type = 'travel';

        if (($type != 'travel') || ($type != 'about_bali')) $type = 'travel';

        return ListArticleResource::collection(Article::orderBy('total_view', 'DESC')->with(['metaTranslations' => function($translation) {
            $translation->where('field', 'meta')->where('language', config('app.locale'));
        }])->where('type', $type)->limit(3)->get());
    }
}
