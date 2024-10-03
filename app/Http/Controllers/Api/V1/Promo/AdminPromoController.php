<?php

namespace App\Http\Controllers\Api\V1\Promo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Promo\StorePromoRequest;
use App\Http\Requests\Api\Promo\UpdatePromoRequest;
use App\Http\Resources\Promo\PromoResource;
use App\Models\Promo;
use App\Models\PromoDay;
use App\Services\Images\ImageService;
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

    public function list()
    {
        return PromoResource::collection(Promo::orderBy('name', 'ASC')->get());
    }


    public function show(Promo $promo)
    {
        $promo = new PromoResource($promo->load('days'));
    
        return $this->successGet($promo);
    }

    public function store(StorePromoRequest $request)
    {
        DB::beginTransaction();
        try {
            $promo          = new Promo();
            $promo->uuid    = $this->generateUuid();

            if (isset($request->image)) $promo->image = $this->saveImage($promo, $request->image);

            $promo->fill($request->validated());

            $promo->save();

            $this->saveDays($promo->id, $request->days);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorMessage($e->getMessage());
        }
        
    }

    public function update(UpdatePromoRequest $request, Promo $promo)
    {
        DB::beginTransaction();
        try {
            $startAt = date("Y-m-d", strtotime($promo->start_at));

            if (($request->start_at != $startAt) && ($request->start_at  < date('Y-m-d'))) return $this->errorValidationResponse('start_at', 'The start at must be a date after or equal to today.');

            if ($request->expired_at <= $request->start_at) return $this->errorValidationResponse('expired_at', 'The expired at must be a date after to start_at.');

            $promo->fill($request->validated());

            if (isset($request->image)) $promo->image = $this->saveImage($promo, $request->image);

            $promo->save();

            $this->saveDays($promo->id, $request->days);
            
            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorMessage($e->getMessage());
        }
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

    protected function saveImage($fastBoat, $requestImage)
    {
        $image = new ImageService($fastBoat->imagePath());
        
        if ($fastBoat->image) {
            $image->delete($fastBoat->image);
        }

        $image->save($requestImage);

        return $image->getImageName();
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

    protected function saveDays($promoId, $days)
    {
        $promos = PromoDay::where('promo_id', $promoId)->get();

        if (isset($promos)) $promos->map->delete();

        foreach ($days as $key => $day) {
            $promoDay           = new PromoDay();
            $promoDay->promo_id = $promoId;
            $promoDay->day      = $day;

            $promoDay->save();
        }
    }
}
