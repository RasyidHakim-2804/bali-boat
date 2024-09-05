<?php

namespace App\Http\Controllers\Api\V1\Faq;

use App\Http\Controllers\Controller;
use App\Http\Resources\Faq\ListFaqResource;
use App\Models\Faq;
use App\Traits\JsonResponsesTrait;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    use JsonResponsesTrait;

    public function top()
    {
        return ListFaqResource::collection(
            Faq::with([
                'translations' => fn($query) => $query->language()
            ])->limit(5)->get()
        );
    }

    public function list()
    {
        return ListFaqResource::collection(
            Faq::with([
                'translations' => fn($query) => $query->language()
            ])->get()
        );
    }
}
