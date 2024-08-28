<?php

namespace App\Http\Controllers\Api\V1\Product\FastBoat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\FastBoat\ScheduleRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\FastBoat\ScheduleResource;
use App\Models\FastBoat;
use App\Models\FastBoatSchedule;
use App\Services\Admin\AdminPermissionService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminScheduleController extends Controller
{
    use JsonResponsesTrait;

    private $permissionService;

    public function __construct()
    {
        $this->permissionService = new AdminPermissionService('schedule');
    }

    public function index(FastBoat $fastBoat)
    {
        $schedules = FastBoatSchedule::with([
            'arrivalHarbor',
            'departureHarbor'
        ])->where('fast_boat_id', '=', $fastBoat->id)->paginate(25);

        return ScheduleResource::collection($schedules);
    }

    public function show(FastBoat $fastBoat, FastBoatSchedule $schedule)
    {
        $schedule = $schedule->load('variants');

        return $this->successGet(
            new ScheduleResource($schedule)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FastBoat $fastBoat, ScheduleRequest $request)
    {
        try {
            DB::beginTransaction();

            $schedule = new FastBoatSchedule();

            $schedule->fast_boat_id          = $fastBoat->id;
            $schedule->departure_harbor_id   = $request->departure_harbor_id;
            $schedule->arrival_harbor_id     = $request->arrival_harbor_id;
            $schedule->departure_time        = $request->departure_time;
            $schedule->arrival_time          = $request->arrival_time;
            $schedule->is_active             = $request->is_active;
            $schedule->save();

            $this->setVariant($schedule, $request);

            DB::commit();

            return $this->successStore();

        } catch (\Throwable $th) {
            
            DB::rollBack();
            
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ScheduleRequest $request, FastBoat $fastBoat, FastBoatSchedule $schedule)
    {
        try {
            DB::beginTransaction();

            $schedule->fast_boat_id          = $fastBoat->id;
            $schedule->departure_harbor_id   = $request->departure_harbor_id;
            $schedule->arrival_harbor_id     = $request->arrival_harbor_id;
            $schedule->departure_time        = $request->departure_time;
            $schedule->arrival_time          = $request->arrival_time;
            $schedule->is_active             = $request->is_active;
            $schedule->save();

            $this->setVariant($schedule, $request);

            DB::commit();

            return $this->successResponse();

        } catch (\Throwable $th) {
            
            DB::rollBack();
            
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FastBoat $fastBoat, FastBoatSchedule $schedule)
    {
        DB::transaction(function() use ($schedule) {
            $schedule->variants()->delete();
            $schedule->delete();
        });

        return new SuccessResource();
    }

    public function setVariant(FastBoatSchedule $schedule, $request)
    {
        foreach ($request->variants as $passengerType => $array) {
            foreach ($array as $ageType => $price) {
                
                $variant = $schedule->variants()->type($passengerType, $ageType)->firstOrNew();
                
                $variant->passenger_type = $passengerType;
                $variant->age_type       = $ageType;
                $variant->price          = $price;
                $variant->save();
            }
        }
    }
}
