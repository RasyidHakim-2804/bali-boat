<?php

namespace App\Http\Controllers\Api\V1\Promo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Promo\StorePromoHighlightRequest;
use App\Http\Resources\Promo\PromoHighlightResource;
use App\Http\Resources\Promo\PromoResource;
use App\Models\PromoHighlight;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminPromoHighlightController extends Controller
{
    use JsonResponsesTrait;

    // protected $permissionService;

    // public function __construct()
    // {
    //     $this->permissionService = new AdminPermissionService('activity_image');
    // }

    public function index()
    {
        return PromoHighlightResource::collection(PromoHighlight::with('promo')->get());
    }

    public function store(StorePromoHighlightRequest $request)
    {
        DB::beginTransaction();
        try {
            PromoHighlight::where('id', '>=', 1)->delete();

            foreach ($request->promos as $position => $promoId) {
                $promoHighlight             = new PromoHighlight();
                $promoHighlight->promo_id   = $promoId;
                $promoHighlight->created_at = now();


                $promoHighlight->save();
            }

            DB::commit();            

            return $this->successResponse();

        } catch (\Exception $e) {
            
            DB::rollBack();
            
            return $this->errorMessage($e->getMessage());
        }
            
    }
}
