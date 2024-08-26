<?php

namespace App\Services\Order;

use App\Models\OrderDetail;

class OrderDetailService
{
    public function create($orderId, $productsRequest, $product, $variant)
    {
        $fastBoat = $product[0]->fastBoat;

        foreach ($productsRequest as $key => $productRequest) {
            $productData = $product[$key];
            $variantData = $variant[$key];

            $variantName  = $fastBoat->name . ' - ' ($product[$key]->is_varied == 1) ? ' - ' . $variantData->name : '';
            $productName  = $productData->name . $variantName;
            $productImage = $fastBoat->image;

            $totalProductPrice = $variantData->price * $productRequest['quantity'];

            $orderDetail                        = new OrderDetail();
            $orderDetail->order_id              = $orderId;
            $orderDetail->fast_boat_id          = $productData->id;
            $orderDetail->schedule_id           = $productData->id;
            $orderDetail->schedule_variant_id   = $variantData?->id;
            $orderDetail->name                  = $productName;
            $orderDetail->quantity              = $productRequest['quantity'];
            $orderDetail->price                 = $variantData->price;
            $orderDetail->price                 = $variantData->price;
            $orderDetail->total_price           = $totalProductPrice;
            $orderDetail->image                 = $productImage;
            
            $orderDetail->save();
        }
    }
}