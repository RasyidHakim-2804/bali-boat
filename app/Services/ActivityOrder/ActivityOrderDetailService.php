<?php

namespace App\Services\ActivityOrder;

use App\Models\ActivityOrderDetail;

class ActivityOrderDetailService
{
    public function create($orderId, $productsRequest, $product, $variant)
    {
        foreach ($productsRequest as $key => $productRequest) {
            $productData    = $product[$key];
            $variantData    = $variant[$key];
            $productImage   = $productData->images()->where('position', 1)->pluck('image')->first();

            $totalProductPrice = $variantData->price * $productRequest['quantity'];

            $orderDetail                        = new ActivityOrderDetail();
            $orderDetail->order_id              = $orderId;
            $orderDetail->product_id            = $productData->id;
            $orderDetail->variant_id            = $variantData?->id;
            $orderDetail->location_id           = $productData->location_id;
            $orderDetail->location_name         = $productData->location->name;
            $orderDetail->passenger_type        = $variantData?->passenger_type;
            $orderDetail->product_name          = $productData->name;
            $orderDetail->variant_name          = $variantData?->name;
            $orderDetail->quantity              = $productRequest['quantity'];
            $orderDetail->price                 = $variantData->price;
            $orderDetail->total_price           = $totalProductPrice;
            $orderDetail->image                 = $productImage;
            $orderDetail->created_at            = now();
            
            $orderDetail->save();
        }
    }
}