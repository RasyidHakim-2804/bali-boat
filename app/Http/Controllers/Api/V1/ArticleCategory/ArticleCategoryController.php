<?php

namespace App\Http\Controllers\Api\V1\ArticleCategory;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleCategory\ArticleCategoryResource;
use App\Models\ArticleCategory;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class ArticleCategoryController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        return ArticleCategoryResource::collection(ArticleCategory::orderBy('name', 'ASC')->get());
    }
}
