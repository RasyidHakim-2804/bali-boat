<?php

namespace App\Http\Controllers\Api\V1\Faq;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Faq\FaqRequest;
use App\Http\Resources\Faq\AdminDetailFaqResource;
use App\Http\Resources\Faq\ListFaqResource;
use App\Models\Faq;
use App\Services\Admin\AdminPermissionService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFaqController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct() 
    {
        // $this->permissionService = new AdminPermissionService('faq');
    }

    public function index()
    {
        return ListFaqResource::collection(
            Faq::with([
                'translations' => fn($query) => $query->language()
            ])->paginate(25)
        );
    }

    public function show(Faq $faq)
    {
        return $this->successGet(
            new AdminDetailFaqResource(
                $faq->load('translations')
            )
        );
    }

    public function store(FaqRequest $request)
    {
        try {
            DB::beginTransaction();

            $faq = new Faq();
            $faq->save();

            foreach ($request->validated('question') as $lang => $translation) {
                $this->setTranslations($faq, $lang, 'question', $translation);
            }

            foreach ($request->validated('answer') as $lang => $translation) {
                $this->setTranslations($faq, $lang, 'answer', $translation);
            }

            DB::commit();
            
            return $this->successStore();

        } catch (\Exception $e) {
            
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function update(FaqRequest $request, Faq $faq)
    {
        try {
            DB::beginTransaction();

            $faq->save();

            foreach ($request->validated('question') as $lang => $translation) {
                $this->setTranslations($faq, $lang, 'question', $translation);
            }

            foreach ($request->validated('answer') as $lang => $translation) {
                $this->setTranslations($faq, $lang, 'answer', $translation);
            }

            DB::commit();
            
            return $this->successResponse();

        } catch (\Exception $e) {
            
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function destroy(Faq $faq)
    {
        try {
            DB::beginTransaction();

            $faq->translations()->delete();
            $faq->delete();

            DB::commit();

            return $this->successResponse();

        } catch (\Exception $e) {
            
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    // private function

    protected function setTranslations(Faq $faq, $lang, $field, $translation)
    {
        $faqTranslation = $faq->translations()->language($lang)->field($field)->firstOrNew();

        $faqTranslation->faq_id       = $faq->id;
        $faqTranslation->language     = $lang;
        $faqTranslation->field        = $field;
        $faqTranslation->translation  = $translation;
        $faqTranslation->save();
    }
}
