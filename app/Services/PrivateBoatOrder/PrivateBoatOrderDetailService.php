<?php

namespace App\Services\PrivateBoatOrder;

use App\Models\PrivateBoatOrderDetail;
use App\Models\OrderDetail;

class PrivateBoatOrderDetailService
{
    public function create($orderId, $productsRequest, $product, $variant)
    {
        foreach ($productsRequest as $key => $productRequest) {
            $productData    = $product[$key];
            $variantData    = $variant[$key];
            $productImage   = $productData->images()->where('position', 1)->pluck('image')->first();

            $totalProductPrice = $variantData->price * $productRequest['quantity'];

            $orderDetail                        = new PrivateBoatOrderDetail();
            $orderDetail->order_id              = $orderId;
            $orderDetail->product_id            = $productData->id;
            $orderDetail->variant_id            = $variantData?->id;
            $orderDetail->pickup_harbor_id      = $productData->pickup_harbor_id;
            $orderDetail->pickup_harbor_name    = $productData->pickupHarbor->name;
            $orderDetail->name                  = $productData->name;
            $orderDetail->quantity              = $productRequest['quantity'];
            $orderDetail->price                 = $variantData->price;
            $orderDetail->total_price           = $totalProductPrice;
            $orderDetail->image                 = $productImage;
            
            $orderDetail->save();
        }
    }
}