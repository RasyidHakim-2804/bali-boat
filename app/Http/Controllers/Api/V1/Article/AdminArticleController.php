<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\StoreArticleRequest;
use App\Http\Requests\Api\Article\UpdateArticleRequest;
use App\Http\Resources\Article\AdminArticleResource;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\ArticleTranslation;
use App\Services\Admin\AdminPermissionService;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminArticleController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('article'));
    }

    public function index()
    {
        // $articles = Article::all();

        // foreach ($articles as $key => $article) {
        //     $this->saveArticleTranslation($article->id, 'body', 'en', $article->body);
        //     $this->saveArticleTranslation($article->id, 'meta', 'en', $article->meta);
        // }

        // $this->permissionService->check('view');

        return AdminArticleResource::collection(Article::latest()->with(['bodyTranslations', 'metaTranslations'])->paginate(25));
    }

    public function show(Article $article)
    {
        // $this->permissionService->check('view');

        $article = new AdminArticleResource($article);
    
        return $this->successGet($article);
    }

    public function store(StoreArticleRequest $request)
    {
        DB::beginTransaction();
        try {
            // $this->permissionService->check('full');

            $article                = new Article();
            $article->total_view    = 0;

            $article->fill($request->validated());

            $article = $this->saveThumbnail($article, $request->thumbnail);

            $article->save();

            foreach ($request->body as $lang => $body) {
                $this->saveArticleTranslation($article->id, 'body', $lang, $body);
            }

            foreach ($request->meta as $lang => $meta) {
                $this->saveArticleTranslation($article->id, 'meta', $lang, $meta);
            }

            $this->saveImages($article, $request->body);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        DB::beginTransaction();
        try {
            // $this->permissionService->check('full');

            $article->fill($request->validated());

            if ($request->image) {
                $this->deleteImage($article);

                $article = $this->saveThumbnail($article, $request->thumbnail);
            }

            $article->save();

            foreach ($request->body as $lang => $body) {
                $this->saveArticleTranslation($article->id, 'body', $lang, $body);
            }

            foreach ($request->meta as $lang => $meta) {
                $this->saveArticleTranslation($article->id, 'meta', $lang, $meta);
            }

            $this->saveImages($article, $request->body);

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

        if ($article->image) $this->deleteImage($article);

        return $this->successStore();
    }

    protected function saveThumbnail($articleImage, $uploadedImage)
    {
        $image = (new ImageService($articleImage->imagePath(), $uploadedImage));

        $image->save();

        $articleImage->image = $image->getImageName();

        return $articleImage;
    }

    protected function saveImages($article, $bodies)
    {
        $uploadedImages = $this->getUsedImagesName($bodies);

        $this->deleteOldImages($article, $uploadedImages);

        ArticleImage::whereIn('image', $uploadedImages)->update(['article_id' => $article->id]);

        $this->deleteUnusedImage();
    }

    protected function getUsedImagesName($bodies)
    {
        $imagesName = [];

        foreach ($bodies as $lang => $body) {
            $imageUrls = explode('img', $body);
            $regex     = "/[\w\-]+\.(jpg|png|gif|jpeg)/";

            if (isset($imageUrls)) {
                foreach ($imageUrls as $key => $imageUrl) {
                    if(preg_match($regex, $imageUrl, $fileName)) {
                        $imagesName[] = $fileName[0];
                    }
                }
            }
        }

        return $imagesName;
    }

    protected function deleteOldImages($article, $uploadedImages)
    {
        $oldImages = ArticleImage::where('article_id', $article->id)->whereNotIn('image', $uploadedImages)->get();
        
        if (isset($oldImages)) {
            foreach ($oldImages as $key => $oldImage) {
                $this->deleteImage($oldImage);
            }
        }
    }

    protected function deleteUnusedImage()
    {
        $unusedImages = ArticleImage::where('article_id', null)->get();

        if (isset($unusedImages)) {
            foreach ($unusedImages as $key => $unusedImage) {
                $this->deleteImage($unusedImage);
            }
        }
    }

    protected function deleteImage($dataImage)
    {
        $image = (new ImageService($dataImage->imagePath()));

        $image->delete($dataImage->image);
    }

    protected function saveArticleTranslation($articleId, $field, $lang, $translation)
    {
        $newTranslation = ArticleTranslation::where('article_id', $articleId)->where('field', $field)->where('language', $lang)->first();

        if (!$newTranslation) $newTranslation = new ArticleTranslation();
        
        $newTranslation->article_id    = $articleId;
        $newTranslation->field         = $field;
        $newTranslation->language      = $lang;
        $newTranslation->translation   = $translation;

        $newTranslation->save();
    }
}
