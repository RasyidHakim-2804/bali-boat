<?php

namespace App\Http\Controllers\Api\V1\Promo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Promo\VerifyPromoRequest;
use App\Models\ActivityOrder;
use App\Models\FastBoatOrder;
use App\Models\Order;
use App\Models\Promo;
use App\Models\PromoDay;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class UserPromoController extends Controller
{
    use JsonResponsesTrait;

    public function __invoke(VerifyPromoRequest $request)
    {
        $promo = Promo::where('code', $request->code)->where('uuid', '!=', null)->first();

        if (!$promo) return $this->errorValidationResponse('code', 'The promo code is invalid.');

        if ($promo->expired_at < now()) return $this->errorValidationResponse('code', 'The promo code is expired.');

        if ($promo->start_at > now()) return $this->errorValidationResponse('code', 'The promo code is not active.');

        if (($promo->is_all_date == 0) && ($promo->for_start_date >= $request->activity_date)) return $this->errorValidationResponse('code', 'The promo code is invalid.');

        if (($promo->is_all_date == 0) && ($promo->for_end_date <= $request->activity_date)) return $this->errorValidationResponse('code', 'The promo code is invalid.');

        $isValidDay = 1;

        if ($promo->is_all_day == 0) {
            $dayOfWeek  = date('N', strtotime($request->activity_date));
            $isValidDay = $this->checkDayValidation($promo, $dayOfWeek);
        }

        if ($isValidDay == 0) return $this->errorValidationResponse('code', 'The promo code is invalid.');

        $orderPromoAppliedCounted = 0;

        if ($promo->duration == 'once') {
            $orderPromoAppliedFastBoat = FastBoatOrder::where('user_id', request()->user()->id)->where('promo_id', $promo->id)->count();
            $orderPromoAppliedActivity = ActivityOrder::where('user_id', request()->user()->id)->where('promo_id', $promo->id)->count();

            $orderPromoAppliedCounted = $orderPromoAppliedFastBoat + $orderPromoAppliedActivity;
        }

        if ($orderPromoAppliedCounted > 0) return $this->errorValidationResponse('code', 'The promo has been used.');

        return $this->successGet($promo);
    }

    protected function checkDayValidation($promo, $activityDay)
    {
        $promoDay = PromoDay::where('promo_id', $promo->id)->where('day', $activityDay)->first();

        if (!$promoDay) return 0;

        return 1;
    }

    // protected function calculateDiskonAmount($promo, $totalPurchased)
    // {
    //     if ($promo->type == 'fixed') return $promo->amount;

    //     return $promoAmount = ($totalPurchased * $promo->amount)/100;

    //     return strval($promoAmount);
    // }
}
