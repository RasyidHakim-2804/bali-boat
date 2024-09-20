<?php

namespace App\Http\Controllers\Api\V1\FaqCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FaqCategory\FaqCategoryRequest;
use App\Http\Resources\FaqCategory\AdminDetailFaqCategoryResource;
use App\Http\Resources\FaqCategory\ListFaqCategoryResource;
use App\Models\FaqCategory;
use App\Traits\JsonResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFaqCategoryController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('article'));
    }

    public function index()
    {
        return ListFaqCategoryResource::collection(
            FaqCategory::with([
                'translations' => fn($query) => $query->language('en')
            ])->paginate(25)
        );
    }

    public function all()
    {
        return ListFaqCategoryResource::collection(
            FaqCategory::with([
                'translations' => fn($query) => $query->language('en')
            ])->get()
        );
    }

    public function show(FaqCategory $faqCategory)
    {
        $faqCategory->load('translations');

        return $this->successGet(
            new AdminDetailFaqCategoryResource($faqCategory)
        );
    }

    public function store(FaqCategoryRequest $request)
    {
        DB::transaction(function() use ($request) {

            $faqCategory       = new FaqCategory();
            $faqCategory->save();
            
            foreach ($request->validated('title') as $lang => $title) {
                $this->setTranslations($faqCategory, $lang, 'title', $title);
            }
        });

        return $this->successStore();
    }

    public function update(FaqCategory $faqCategory, FaqCategoryRequest $request)
    {
        DB::transaction(function() use ($request, $faqCategory) {
            
            foreach ($request->validated('title') as $lang => $title) {
                $this->setTranslations($faqCategory, $lang, 'title', $title);
            }
        });

        return $this->successResponse();
    }

    public function destroy(FaqCategory $faqCategory)
    {
        DB::beginTransaction();

        try {
        
            $faqCategory->delete();

            DB::commit();

            return $this->successResponse();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Private function

    protected function setTranslations(FaqCategory $faqCategory, $lang, $field, $translation)
    {
        $faqCategoryTranslation = $faqCategory->translations()->language($lang)->field($field)->firstOrNew();
        
        $faqCategoryTranslation->faq_category_id   = $faqCategory->id;
        $faqCategoryTranslation->language          = $lang;
        $faqCategoryTranslation->field             = $field;
        $faqCategoryTranslation->translation       = $translation;
        $faqCategoryTranslation->save();
    }
}
