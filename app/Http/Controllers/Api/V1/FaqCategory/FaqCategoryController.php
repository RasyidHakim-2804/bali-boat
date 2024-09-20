<?php

namespace App\Http\Controllers\Api\V1\FaqCategory;

use App\Http\Controllers\Controller;
use App\Http\Resources\Faq\ListFaqResource;
use App\Http\Resources\FaqCategory\ListFaqCategoryResource;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Traits\JsonResponsesTrait;
use Illuminate\Http\Request;

class FaqCategoryController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        return ListFaqCategoryResource::collection(
            FaqCategory::with([
                'translations'  => fn($query) => $query->language()
            ])->get()
        );
    }

    public function show(FaqCategory $faqCategory)
    {
        return ListFaqResource::collection(
            Faq::with([
                'translations'  => fn($query)   => $query->language()
            ])->where('category_id', '=', $faqCategory->id)->paginate(25)
        );
    }
}
