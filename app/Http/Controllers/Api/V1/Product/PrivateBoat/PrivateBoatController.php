<?php

namespace App\Http\Controllers\Api\V1\Product\PrivateBoat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InactivePrivateBoatCalender\TopPrivateBoatDateRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\PrivateBoat\PrivateBoatResource;
use App\Models\InactivePrivateBoatCalender;
use App\Models\PrivateBoat;
use App\Traits\JsonResponsesTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class PrivateBoatController extends Controller
{
    use JsonResponsesTrait;

    public function search()
    {
        $privateBoats = QueryBuilder::for(PrivateBoat::class)
        ->allowedFilters([
            AllowedFilter::partial('harbor_id', 'pickup_harbor_id'),
            AllowedFilter::scope('people_count'),
            AllowedFilter::scope('date'),
        ])
        ->allowedSorts([
            AllowedSort::field('price')
        ])
        ->with([
            'tags.translations'     => fn($query) => $query->language(),
            'thumbnailImage',
            'pickupHarbor'
        ])
        ->selectThumbnailVariantPrice()
        ->paginate(25);

        return PrivateBoatResource::collection($privateBoats);
    }

    public function show(PrivateBoat $privateBoat)
    {
        $privateBoat = $privateBoat->load([
            'tags.translations'         => fn($query) => $query->language(),
            'includes'                  => fn($query) => $query->orderBy('position', 'ASC'),
            'itineraries'               => fn($query) => $query->orderBy('position', 'ASC'),
            'translations'              => fn($query) => $query->language(),
            'includes.translations'     => fn($query) => $query->language(),
            'itineraries.translations'  => fn($query) => $query->language(),
            'specification',
            'pickupHarbor',
            'images',
            'variants'
        ]);

        return $this->successGet(
            new PrivateBoatResource($privateBoat)
        );
    }

    public function top()
    {
        $query = "id DESC";

        if (request()->get('pickup_harbor_id')) {
            $pickup_harbor_id = request()->get('pickup_harbor_id');
            $query = "pickup_harbor_id = '{$pickup_harbor_id}' DESC";
        }

        $privateBoats = QueryBuilder::for(PrivateBoat::class)
        ->allowedFilters([
            AllowedFilter::scope('date'),
        ])
        ->with([
            'tags.translations'     => fn($query) => $query->language(),
            'thumbnailImage',
            'pickupHarbor'
        ])
        ->selectThumbnailVariantPrice()
        ->orderByRaw($query)
        ->limit(3);

        if (request('pickup_harbor_id')) {
            $privateBoats = $privateBoats->where('pickup_harbor_id', '=', request('pickup_harbor_id'));
        }

        if (request('except_id')) {
            $privateBoats = $privateBoats->where('id', '!=', request('except_id'));
        }

        return PrivateBoatResource::collection($privateBoats->get());
    }


    public function validateDate(PrivateBoat $privateBoat, TopPrivateBoatDateRequest $request)
    {
        if (!InactivePrivateBoatCalender::where('date', $request->date)->where('product_id', $privateBoat->id)->exists()) {
            return $this->successResponse();
        }

        if (app()->getLocale() == 'id') {
            return $this->errorMessage(
                'Mohon maaf, tanggal yang anda minta tidak aktif. Coba pesan di tanggal lain'
            );
        } else {
            return $this->errorMessage(
                'Sorry, the date you requested is not active. Try to book on another date'
            );
        }
    }
}
