<?php

namespace App\Http\Controllers\Api\V1\Product\FastBoat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\FastBoat\FastBoatResource;
use App\Http\Resources\Product\FastBoat\ScheduleResource;
use App\Models\FastBoat;
use App\Models\FastBoatSchedule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class FastBoatController extends Controller
{   
    public function search()
    {
        $schedules = QueryBuilder::for(FastBoatSchedule::class)
        ->defaultSort('departure_time')
        ->selectVariantPrice()
        ->allowedFilters([
            AllowedFilter::exact('fast_boat_id'),
            AllowedFilter::scope('from_to'),
            AllowedFilter::scope('select_date'),
            AllowedFilter::scope('departure_time')->default('morning'),
        ])
        ->allowedSorts([
            AllowedSort::field('price','variant_price'),
            AllowedSort::field('recomended','departure_time'),
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

    public function list()
    {
        return FastBoatResource::collection(FastBoat::all('id', 'name'));
    }
}
