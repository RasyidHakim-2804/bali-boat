<?php

namespace App\Services\Order;

use App\Models\OrderAdditional;

class OrderAdditionalService
{
    public function create($orderId, $additionalRequests, $additionals)
    {
        foreach ($additionalRequests as $key => $additionalRequest) {
            $productAdditional      = $additionals[$key];
            $additionalData         = $productAdditional->additionalData;
            $totalProductPrice      = $additionalData->price * $additionalRequest['quantity'];
            $totalUsdProductPrice   = $additionalData->usd_price * $additionalRequest['quantity'];
            $totalEurProductPrice   = $additionalData->eur_price * $additionalRequest['quantity'];
            $totalJpyProductPrice   = $additionalData->jpy_price * $additionalRequest['quantity'];
            $totalCnyProductPrice   = $additionalData->cny_price * $additionalRequest['quantity'];

            $orderDetail                    = new OrderAdditional();
            $orderDetail->order_id          = $orderId;
            $orderDetail->product_id        = $productAdditional->product_id;
            $orderDetail->variant_id        = $productAdditional->variant_id;
            $orderDetail->additional_id     = $productAdditional->additional_id;
            $orderDetail->name              = $additionalData->name;
            $orderDetail->description       = $additionalData->description;
            $orderDetail->quantity          = $additionalRequest['quantity'];
            $orderDetail->price             = $additionalData->price;
            $orderDetail->usd_price         = $additionalData->usd_price;
            $orderDetail->jpy_price         = $additionalData->jpy_price;
            $orderDetail->cny_price         = $additionalData->cny_price;
            $orderDetail->eur_price         = $additionalData->eur_price;
            $orderDetail->total_price       = $totalProductPrice;
            $orderDetail->total_usd_price   = $totalUsdProductPrice;
            $orderDetail->total_eur_price   = $totalEurProductPrice;
            $orderDetail->total_jpy_price   = $totalJpyProductPrice;
            $orderDetail->total_cny_price   = $totalCnyProductPrice;
            
            $orderDetail->save();
        }
    }
}