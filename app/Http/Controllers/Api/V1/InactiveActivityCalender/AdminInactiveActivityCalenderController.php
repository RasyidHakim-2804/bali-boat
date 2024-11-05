<?php

namespace App\Http\Controllers\Api\V1\InactiveActivityCalender;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InactiveActivityCalender\GetInactiveActivityCalenderRequest;
use App\Http\Requests\Api\InactiveActivityCalender\StoreInactiveActivityCalenderRequest;
use App\Models\InactiveActivityCalender;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminInactiveActivityCalenderController extends Controller
{
    use JsonResponsesTrait;

    public function index(GetInactiveActivityCalenderRequest $request)
    {
        $calender = InactiveActivityCalender::where('product_id', $request->product_id)->whereMonth('date', $request->month)->whereYear('date', $request->year)->get();

        return $this->successGet($calender);
    }

    public function show(InactiveActivityCalender $inactive_product_calender)
    {
        return $this->successGet($inactive_product_calender);
    }

    public function store(StoreInactiveActivityCalenderRequest $request)
    {
        // DB::beginTransaction();

        try {
            if (isset($request->dates)) {
                InactiveActivityCalender::whereNotIn('date', $request->dates)->delete();

                foreach ($request->dates as $key => $date) {
                    $calender             = InactiveActivityCalender::where('product_id', $request->product_id)->whereDate('date', $date)->firstOrNew();
                    $calender->product_id = $request->product_id;
                    $calender->date       = $date;

                    $calender->save();
                }
            } else {
                InactiveActivityCalender::where('product_id', $request->product_id)->whereMonth('date', $request->month)->whereYear('date', $request->year)->delete();
            }

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            // DB::rollBack();

            $this->errorServer($e->getMessage());
        }
    }
}
