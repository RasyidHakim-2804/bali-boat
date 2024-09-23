<?php

namespace App\Http\Controllers\Api\V1\Product\FastBoat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\FastBoat\ScheduleResource;
use App\Http\Resources\Product\FastBoat\ScheduleAndFastBoatResource;
use App\Models\FastBoat;
use App\Models\FastBoatSchedule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class FastBoatScheduleController extends Controller
{
    public function firstSearch()
    {
        $schedules = QueryBuilder::for(FastBoatSchedule::class)
        ->defaultSort('departure_time')
        ->selectVariantPrice()
        ->allowedFilters([
            AllowedFilter::exact('fast_boat_id'),
            AllowedFilter::scope('from_to'),
            AllowedFilter::scope('departure_time')->default('morning'),
        ])
        ->allowedSorts([
            AllowedSort::field('price','variant_price'),
            AllowedSort::field('departure_time')
        ])
        ->with([
            'fastBoat',
            'departureHarbor:id,name',
            'arrivalHarbor:id,name',
            'variants'
        ])
        ->boatActive()
        ->active()
        ->paginate(25);

        $fastBoat = FastBoat::active()->get(['id', 'name']);

        return new ScheduleAndFastBoatResource(
            (object) [
                'schedules'     => $schedules->setPath($this->generateUrl($schedules)),
                'fast_boats'    => $fastBoat,
            ]
        );
    }

    public function index()
    {
        $schedules = QueryBuilder::for(FastBoatSchedule::class)
        ->defaultSort('departure_time')
        ->selectVariantPrice()
        ->allowedFilters([
            AllowedFilter::exact('fast_boat_id'),
            AllowedFilter::scope('from_to'),
            AllowedFilter::scope('departure_time')->default('morning'),
        ])
        ->allowedSorts([
            AllowedSort::field('price','variant_price'),
            AllowedSort::field('departure_time')
        ])
        ->with([
            'fastBoat',
            'departureHarbor:id,name',
            'arrivalHarbor:id,name',
            'variants'
        ])
        ->boatActive()
        ->active()
        ->paginate(25);

        return ScheduleResource::collection($schedules);
    }

    // Private function

    private function generateUrl($schedules)
    {
        $currentUrl  = $schedules->url($schedules->currentPage());

        $modifiedUrl = str_replace('-first', '', $currentUrl);
        $modifiedUrl = str_replace('?page=' . $schedules->currentPage(),'', $modifiedUrl);

        return $modifiedUrl;
    }
}
