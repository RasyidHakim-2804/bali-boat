<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\StoreArticleImagesRequest;
use App\Http\Resources\Article\ArticleImageResource;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Services\Admin\AdminPermissionService;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminArticleImagesController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('product'));
    }

    public function index(Article $article)
    {
        // $this->permissionService->check('view');

        $images = ArticleImageResource::collection($article->images()->orderBy('position', 'ASC')->get());
    
        return $this->successGet($images);
    }

    public function store(StoreArticleImagesRequest $request)
    {
        // $this->permissionService->check('full');

        $articleImage = new ArticleImage();
        $articleImage = $this->saveImages($articleImage, $request->image);

        $articleImage->save();
        
        return $this->successStoreWithData(new ArticleImageResource($articleImage));
    }

    protected function saveImages($articleImage, $uploadedImage)
    {
        // $this->permissionService->check('full');

        $image = (new ImageService($articleImage->imagePath(), $uploadedImage));

        $image->save();

        $articleImage->image = $image->getImageName();

        return $articleImage;
    }
}
