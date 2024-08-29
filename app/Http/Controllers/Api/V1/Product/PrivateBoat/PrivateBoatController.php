<?php

namespace App\Http\Controllers\Api\V1\Product\PrivateBoat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\PrivateBoat\PrivateBoatResource;
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
            AllowedFilter::partial('harbor_id', 'pickup_harbor_id')
        ])
        ->allowedSorts([
            AllowedSort::field('price')
        ])
        ->with([
            'tags.translations'     => fn($query) => $query->language(),
            'thumbnailImage',
            'thumbnailVariant',
            'pickupHarbor'
        ])
        ->selectVariantPrice()
        ->paginate(25);

        return PrivateBoatResource::collection($privateBoats);
    }

    public function show(PrivateBoat $privateBoat)
    {
        $privateBoat = $privateBoat->load([
            'tags.translations'     => fn($query) => $query->language(),
            'includes'              => fn($query) => $query->orderBy('position', 'ASC'),
            'translations'          => fn($query) => $query->language(),
            'includes.translations' => fn($query) => $query->language(),
            'specifications'        => fn($query) => $query->language(),
            'pickupHarbor',
            'images',
            'variants',
            'defaultVariant'
        ]);

        return $this->successGet(
            new PrivateBoatResource($privateBoat)
        );
    }

    public function top()
    {
        $privateBoats = PrivateBoat::with([
            'tags.translations'     => fn($query) => $query->language(),
            'thumbnailImage',
            'thumbnailVariant',
            'pickupHarbor'
        ])
        ->orderBy('id', 'DESC')
        ->limit(4)
        ->get();

        return PrivateBoatResource::collection($privateBoats);
    }
}
