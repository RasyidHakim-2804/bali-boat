<?php

namespace App\Http\Controllers\Api\V1\InactiveFastBoatCalender;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InactiveFastBoatCalender\GetInactiveFastBoatCalenderRequest;
use App\Http\Requests\Api\InactiveFastBoatCalender\StoreInactiveFastBoatCalenderRequest;
use App\Models\InactiveFastBoatCalender;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminInactiveFastBoatCalenderController extends Controller
{
    use JsonResponsesTrait;

    public function index(GetInactiveFastBoatCalenderRequest $request)
    {
        $calender = InactiveFastBoatCalender::where('product_id', $request->product_id)->whereMonth('date', $request->month)->whereYear('date', $request->year)->get();

        return $this->successGet($calender);
    }

    public function show(InactiveFastBoatCalender $inactive_product_calender)
    {
        return $this->successGet($inactive_product_calender);
    }

    public function store(StoreInactiveFastBoatCalenderRequest $request)
    {
        // DB::beginTransaction();
        try {
            if (isset($request->dates)) {
                InactiveFastBoatCalender::whereIn('date', '!=', $request->date)->delete();

                foreach ($request->dates as $key => $date) {
                    $calender             = InactiveFastBoatCalender::where('product_id', $request->product_id)->whereDate('date', $date)->firstOrNew();
                    $calender->product_id = $request->product_id;
                    $calender->date       = $date;

                    $calender->save();
                }
            } else {
                InactiveFastBoatCalender::where('product_id', $request->product_id)->whereYear('date', $request->year)->whereMonth('date', $request->month)->delete();
            }

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            // DB::rollBack();

            $this->errorServer($e->getMessage());
        }
    }
}
