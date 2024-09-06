<?php

namespace App\Services\FastBoatOrder;

use App\Models\FastBoatOrder;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class FastBoatOrderService
{
    public function create($request, $products, $variants, $user, $promo)
    {
        $promoAmount    = 0;
        $totalPurchased = $this->calculateTotalPurchased($request, $variants);

        if ($promo) $promoAmount = $this->calculatePromoAmount($totalPurchased, $promo);

        $totalPurchasedAfterPromo = $totalPurchased - $promoAmount;

        if ($totalPurchasedAfterPromo < 0) $totalPurchasedAfterPromo = 0;

        $grandTotalPurchased = $totalPurchasedAfterPromo;

        $order                          = new FastBoatOrder();
        $order->user_id                 = $user->id;
        $order->uuid                    = $this->generateUuid();
        $order->promo_amount            = number_format($promoAmount, 2, '.', '');
        $order->promo_id                = $promo?->id;
        $order->total_purchased         = number_format($totalPurchased, 2, '.', '');
        $order->grand_total_purchased   = number_format($grandTotalPurchased, 2, '.', '');
        $order->status                  = 'waiting_for_payment';
        $order->pic_name                = $request->pic_name;
        $order->pic_email               = $request->pic_email;
        $order->pic_phone_number        = $request->pic_phone_number;
        $order->activity_date           = $request->activity_date;
        $order->return_date             = $request->return_date;

        $order->save();

        Log::channel('order')->info(PHP_EOL . '' . PHP_EOL .'New Order'. PHP_EOL .'' . $order);

        return $order;
    }

    protected function calculateTotalPurchased($request, $variantsData)
    {
        $totalPurchased = 0;

        foreach ($request->products as $key => $product) {
            $totalPurchased = $totalPurchased + ($product['quantity'] * $variantsData[$key]->price);
        }

        return $totalPurchased;
    }

    protected function calculateTotalAdditionalPurchased($request, $additionalsData)
    {
        $totalPurchased = $totalUsdPurchased = $totalEurPurchased = $totalJpyPurchased = $totalCnyPurchased = 0;

        if (isset($request->additionals)) {
            foreach ($request->additionals as $key => $additional) {
                $additionalData  = $additionalsData[$key]->additionalData;

                $totalPurchased     = $totalPurchased + ($additional['quantity'] * $additionalData->price);
                $totalUsdPurchased  = $totalUsdPurchased + ($additional['quantity'] * $additionalData->usd_price);
                $totalEurPurchased  = $totalEurPurchased + ($additional['quantity'] * $additionalData->eur_price);
                $totalJpyPurchased  = $totalJpyPurchased + ($additional['quantity'] * $additionalData->jpy_price);
                $totalCnyPurchased  = $totalCnyPurchased + ($additional['quantity'] * $additionalData->cny_price);
            }
        }

        return [
            'idr'   => $totalPurchased,
            'usd'   => $totalUsdPurchased,
            'eur'   => $totalEurPurchased,
            'jpy'   => $totalJpyPurchased,
            'cny'   => $totalCnyPurchased
        ];
    }

    protected function calculatePromoAmount($totalPurchased, $promo)
    {
        $promoAmount = $promo->amount;

        if ($promo->type == 'percentage') {
            $promoAmount = ($totalPurchased * $promo->amount)/100;
        }

        if (($promo->type == 'fixed') && ($promo->amount > $totalPurchased))
            $promoAmount = $totalPurchased;
        
        $promoAmount = floatval($promoAmount);

        return $promoAmount;
    }

    protected function generateUuid()
    {
        $sameUuid = 1;

        while ($sameUuid > 0) {
            $uuid     = Str::random(15);
            $sameUuid = FastBoatOrder::where('uuid', $uuid)->count();
        }
        
        return $uuid;
    }
    
}