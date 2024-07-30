<?php

namespace App\Http\Controllers\Web\V1\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Stripe\CheckoutService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class OrderResponseController extends Controller
{
    use JsonResponsesTrait;

    public function complete()
    {
        $url = env('FRONTEND_URL') . '/orders/success';

        return redirect($url);
    }

    public function cancel(Order $order)
    {
        DB::beginTransaction();
        try {
            if ($order->status != 'waiting_for_payment') return $this->errorValidationResponse('order_id', 'Order is invalid.');

            $expireStripeCheckout = (new CheckoutService)->expire($order->payment->checkout_id);

            if ($expireStripeCheckout['status'] == 'fail') return $this->errorServer($expireStripeCheckout['message']);

            DB::commit();

            $url = env('FRONTEND_URL') . '/orders/cancel';

            return redirect($url);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer();
        }
    }
}
