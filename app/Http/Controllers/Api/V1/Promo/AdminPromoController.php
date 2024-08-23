<?php

namespace App\Http\Controllers\Api\V1\Promo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Promo\StorePromoRequest;
use App\Http\Requests\Api\Promo\UpdatePromoRequest;
use App\Http\Resources\Promo\PromoResource;
use App\Models\Promo;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminPromoController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        return PromoResource::collection(Promo::orderBy('id', 'DESC')->paginate(25));
    }

    public function show(Promo $promo)
    {
        $promo = new PromoResource($promo);
    
        return $this->successGet($promo);
    }

    public function store(StorePromoRequest $request)
    {
        DB::beginTransaction();
        try {
            $promo       = new Promo();
            $promo->uuid = $this->generateUuid();

            $promo->fill($request->validated());

            $promo->save();

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorMessage($e->getMessage());
        }
        
    }

    public function update(UpdatePromoRequest $request, Promo $promo)
    {
        $startAt = date("Y-m-d", strtotime($promo->start_at));

        if (($request->start_at != $startAt) && ($request->start_at  < date('Y-m-d'))) return $this->errorValidationResponse('start_at', 'The start at must be a date after or equal to today.');

        if ($request->expired_at <= $request->start_at) return $this->errorValidationResponse('expired_at', 'The expired at must be a date after to start_at.');

        $promo->fill($request->validated());

        $promo->save();

        return $this->successStore();
    }

    public function destroy(Promo $promo)
    {
        DB::beginTransaction();
        try {
            $promo->delete();

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorMessage($e->getMessage());
        }
    }

    protected function generateUuid()
    {
        $sameUuid = 1;

        while ($sameUuid > 0) {
            $uuid     = Str::random(15);
            $sameUuid = Promo::where('uuid', $uuid)->count();
        }
        
        return $uuid;
    }
}
