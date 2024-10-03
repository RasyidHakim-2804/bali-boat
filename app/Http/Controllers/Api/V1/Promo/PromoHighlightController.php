<?php

namespace App\Http\Controllers\Api\V1\Promo;

use App\Http\Controllers\Controller;
use App\Http\Resources\Promo\PromoHighlightResource;
use App\Models\Promo;
use App\Models\PromoHighlight;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class PromoHighlightController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        return PromoHighlightResource::collection(PromoHighlight::with('promo')->get());
    }

    public function show(Promo $promo)
    {
        return new PromoHighlightResource($promo);
    }
}
