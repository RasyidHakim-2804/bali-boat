<?php

namespace App\Http\Controllers\Api\V1\Promo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Promo\VerifyPromoRequest;
use App\Models\Order;
use App\Models\Promo;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class PromoController extends Controller
{
    use JsonResponsesTrait;

    public function __invoke(VerifyPromoRequest $request)
    {
        $promo = Promo::where('code', $request->code)->where('uuid', '!=', null)->first();

        if (!$promo) return $this->errorValidationResponse('code', 'The promo code is invalid.');

        if ($promo->expired_at < now()) return $this->errorValidationResponse('code', 'The promo code is expired.');

        if ($promo->start_at > now()) return $this->errorValidationResponse('code', 'The promo code is not active.');

        $orderPromoAppliedCounted = 0;

        if (($request->user_id) && ($promo->duration == 'once')) $orderPromoAppliedCounted = Order::where('user_id', $request->user_id)->where('promo_id', $promo->id)->count();

        if (($request->email) && (!$request->user_id) && ($promo->duration == 'once')) $orderPromoAppliedCounted = Order::where('guest_email', $request->email)->where('promo_id', $promo->id)->count();

        if ($orderPromoAppliedCounted > 0) return $this->errorValidationResponse('code', 'The promo has been used.');

        return $this->successGet($promo);
    }

    // protected function calculateDiskonAmount($promo, $totalPurchased)
    // {
    //     if ($promo->type == 'fixed') return $promo->amount;

    //     return $promoAmount = ($totalPurchased * $promo->amount)/100;

    //     return strval($promoAmount);
    // }
}
