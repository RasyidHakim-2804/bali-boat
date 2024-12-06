<?php

namespace App\Services\FastBoatOrder;

use App\Models\FastBoatOrderDetail;
use App\Models\OrderDetail;

class FastBoatOrderDetailService
{
    public function create($orderId, $productsRequest, $product, $variant)
    {
        $fastBoat = $product[0]->fastBoat;

        foreach ($productsRequest as $key => $productRequest) {
            $productData    = $product[$key];
            $variantData    = $variant[$key];
            $productImage   = $fastBoat->image;

            $totalProductPrice = $variantData->price * $productRequest['quantity'];

            $orderDetail                        = new FastBoatOrderDetail();
            $orderDetail->order_id              = $orderId;
            $orderDetail->fast_boat_id          = $productData->fast_boat_id;
            $orderDetail->schedule_id           = $productData->id;
            $orderDetail->schedule_variant_id   = $variantData?->id;
            $orderDetail->departure_harbor_id   = $productData->departure_harbor_id;
            $orderDetail->arrival_harbor_id     = $productData->arrival_harbor_id;
            $orderDetail->departure_harbor_name = $productData->departureHarbor->name;
            $orderDetail->arrival_harbor_name   = $productData->arrivalHarbor->name;
            $orderDetail->departure_time        = $productData->departure_time;
            $orderDetail->arrival_time          = $productData->arrival_time;
            $orderDetail->name                  = $productData->fastBoat->name;
            $orderDetail->passenger_type        = $variantData->passenger_type;
            $orderDetail->age_type              = $variantData->age_type;
            $orderDetail->quantity              = $productRequest['quantity'];
            $orderDetail->price                 = $variantData->price;
            $orderDetail->total_price           = $totalProductPrice;
            $orderDetail->image                 = $productImage;
            
            $orderDetail->save();
        }
    }
}