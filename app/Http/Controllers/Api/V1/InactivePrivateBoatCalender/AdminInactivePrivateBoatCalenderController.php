<?php

namespace App\Http\Controllers\Api\V1\InactivePrivateBoatCalender;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InactivePrivateBoatCalender\GetInactivePrivateBoatCalenderRequest;
use App\Http\Requests\Api\InactivePrivateBoatCalender\StoreInactivePrivateBoatCalenderRequest;
use App\Models\InactivePrivateBoatCalender;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminInactivePrivateBoatCalenderController extends Controller
{
    use JsonResponsesTrait;

    public function index(GetInactivePrivateBoatCalenderRequest $request)
    {
        $calender = InactivePrivateBoatCalender::where('product_id', $request->product_id)->whereMonth('date', $request->month)->whereYear('date', $request->year)->get();

        return $this->successGet($calender);
    }

    public function show(InactivePrivateBoatCalender $inactive_product_calender)
    {
        return $this->successGet($inactive_product_calender);
    }

    public function store(StoreInactivePrivateBoatCalenderRequest $request)
    {
        // DB::beginTransaction();

        try {
            InactivePrivateBoatCalender::whereNotIn('date', $request->dates)->delete();

            if (isset($request->dates)) {
                foreach ($request->dates as $key => $date) {
                    $calender             = InactivePrivateBoatCalender::where('product_id', $request->product_id)->whereDate('date', $date)->firstOrNew();
                    $calender->product_id = $request->product_id;
                    $calender->date       = $date;

                    $calender->save();
                }
            } else {
                InactivePrivateBoatCalender::where('product_id', $request->product_id)->whereMonth('date', $request->month)->whereYear('date', $request->year)->delete();
            }

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            // DB::rollBack();

            $this->errorServer($e->getMessage());
        }
    }
}
