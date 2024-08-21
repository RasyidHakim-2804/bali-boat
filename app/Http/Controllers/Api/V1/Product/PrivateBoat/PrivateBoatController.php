<?php

namespace App\Http\Controllers\Api\V1\Product\PrivateBoat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\PrivateBoat\PrivateBoatResource;
use App\Models\PrivateBoat;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class PrivateBoatController extends Controller
{
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
            'tags','thumbnailImage', 'defaultVariant', 'pickupHarbor'
        ])
        ->selectVariantPrice()
        ->paginate(25);

        return PrivateBoatResource::collection($privateBoats);
    }

    public function show(PrivateBoat $privateBoat)
    {
        $privateBoat = $privateBoat->load([
            'tags',
            'pickupHarbor',
            'highlightTranslations' => fn($query) => $query->where('language', app()->getLocale()),
            'metaTranslations'      => fn($query) => $query->where('language', app()->getLocale()),
            'includes'              => fn($query) => $query->where('language', app()->getLocale()),
            'specifications'        => fn($query) => $query->where('language', app()->getLocale()),
            'images',
        ]);

        return new SuccessResource(
            data: new PrivateBoatResource($privateBoat)
        );
    }
}
