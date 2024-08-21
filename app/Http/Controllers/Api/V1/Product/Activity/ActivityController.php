<?php

namespace App\Http\Controllers\Api\V1\Product\Activity;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\Activity\ActivityResource;
use App\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ActivityController extends Controller
{
    public function search()
    {
        $activities = QueryBuilder::for(Activity::class)
        ->allowedFilters([
            AllowedFilter::exact('location_id'),
            AllowedFilter::scope('person',)
        ])
        ->allowedSorts([
            AllowedSort::field('price')
        ])
        ->with([
            'tags','thumbnailImage', 'defaultVariant', 'location'
        ])
        ->selectVariantPrice()
        ->paginate(25);

        return ActivityResource::collection($activities);
    }

    public function show(Activity $activity)
    {
        $activity = $activity->load([
            'tags',
            'defaultVariant',
            'location',
            'highlightTranslations'   => fn($query) => $query->where('language', app()->getLocale()),
            'descriptionTranslations' => fn($query) => $query->where('language', app()->getLocale()),
            'includes'                => fn($query) => $query->where('language', app()->getLocale()),
            'excludes'                => fn($query) => $query->where('language', app()->getLocale()),
            'itinararies'             => fn($query) => $query->where('language', app()->getLocale()),
            'images',
        ]);

        return new SuccessResource(
            data: new ActivityResource($activity)
        );
    }
}
