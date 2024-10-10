<?php

namespace App\Http\Controllers\Api\V1\Product\Activity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InactiveActivityCalender\TopActivityDateRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\Activity\ActivityResource;
use App\Models\Activity;
use App\Models\InactiveActivityCalender;
use App\Traits\JsonResponsesTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ActivityController extends Controller
{
    use JsonResponsesTrait;

    public function search()
    {
        $activities = QueryBuilder::for(Activity::class)
        ->allowedFilters([
            AllowedFilter::exact('location_id'),
            AllowedFilter::scope('people_count'),
            AllowedFilter::scope('date')
        ])
        ->allowedSorts([
            AllowedSort::field('price')
        ])
        ->with([
            'tags.translations'         => fn($query) => $query->language(),
            'thumbnailImage',
            'location'
        ])
        ->selectThumbnailVariantPrice()
        ->paginate(25);

        return ActivityResource::collection($activities);
    }

    public function show(Activity $activity)
    {
        $activity = $activity->load([
            'tags.translations'         => fn($query) => $query->language(),
            'includes'                  => fn($query) => $query->orderBy('position', 'ASC'),
            'excludes'                  => fn($query) => $query->orderBy('position', 'ASC'),
            'itineraries'               => fn($query) => $query->orderBy('position', 'ASC'),
            'translations'              => fn($query) => $query->language(),
            'includes.translations'     => fn($query) => $query->language(),
            'excludes.translations'     => fn($query) => $query->language(),
            'itineraries.translations'  => fn($query) => $query->language(),
            'location',
            'images',
            'variants',
        ]);

        return $this->successGet(
            new ActivityResource($activity)
        );
    }

    public function top()
    {
        $privateBoats = Activity::with([
            'tags.translations' => fn($query) => $query->language(),
            'thumbnailImage',
            'location'
        ])
        ->selectThumbnailVariantPrice()
        ->orderBy('id', 'DESC')
        ->limit(4)
        ->get();

        return ActivityResource::collection($privateBoats);
    }

    public function validateDate(Activity $activity, TopActivityDateRequest $request)
    {
        if (!InactiveActivityCalender::where('date', $request->date)->where('product_id', $activity->id)->exists()) {
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
