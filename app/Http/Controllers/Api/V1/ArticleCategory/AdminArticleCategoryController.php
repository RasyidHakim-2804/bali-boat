<?php

namespace App\Http\Controllers\Api\V1\ArticleCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ArticleCategory\StoreArticleCategoryRequest;
use App\Http\Resources\Article\AdminArticleResource;
use App\Http\Resources\ArticleCategory\ArticleCategoryResource;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Services\Admin\AdminPermissionService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminArticleCategoryController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('article'));
    }

    public function index()
    {
        return ArticleCategoryResource::collection(ArticleCategory::latest()->paginate(25));
    }

    public function show(ArticleCategory $articleCategory)
    {
        // $this->permissionService->check('view');

        $articleCategory = new AdminArticleResource($articleCategory);
    
        return $this->successGet($articleCategory);
    }

    public function store(StoreArticleCategoryRequest $request)
    {
        DB::beginTransaction();
        try {
            // $this->permissionService->check('full');

            $article = new ArticleCategory();

            $article->fill($request->validated());

            $article->save();
            
            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function destroy(Article $article)
    {
        // $this->permissionService->check('full');

        $article->delete();

        return $this->successStore();
    }
}
