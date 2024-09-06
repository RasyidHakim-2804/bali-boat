<?php

namespace App\Http\Controllers\Api\V1\Webhook\Xendit;

use App\Http\Controllers\Controller;
use App\Models\OrderPayment;
use App\Services\Email\OrderPaymentEmailService;
use App\Services\Logs\OrderPaymentLogService;
use App\Services\Xendit\XenditInvoiceService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class XenditInvoiceWebhookController extends Controller
{
    use JsonResponsesTrait;

    public function __invoke(Request $request)
    {
        DB::beginTransaction();

        try {
            $callbackToken = $request->header('x-callback-token') ? $request->header('x-callback-token') : '';

            if (env('XENDIT_WEBHOOK_TOKEN') != $callbackToken) return $this->errorNotFound();

            $orderPayment = OrderPayment::where('invoice_id', $request->id)->first();

            if (!$orderPayment) return $this->successResponseMessage('This invoice webhook not for surya bintang officials.');

            if ($orderPayment->type == 'fast_boat') $order = $orderPayment->fastBoatOrder;

            if ($orderPayment->type == 'private_boat') $order = $orderPayment->privateBoatOrder;

            if ($orderPayment->type == 'activity') $order = $orderPayment->activityOrder;

            $invoice        = (new XenditInvoiceService)->show($request->id);
            $paymentStatus  = (strtolower($invoice['status']) == 'settled') ? 'paid' : strtolower($invoice['status']);
            $orderStatus    = $order->status;

            if ($paymentStatus == 'expired') $orderStatus = 'failed';

            if (($orderStatus == 'failed') && ($order->status != $orderStatus)) (new OrderPaymentEmailService)->expired($order, $orderPayment->type);

            if ($paymentStatus == 'paid') $orderStatus = 'paid';

            // if ($order->status != $orderStatus) (new OrderPaymentEmailService)->paid($order, $orderPayment->type);

            $orderPayment->status          = $paymentStatus;
            $orderPayment->payment_method  = strtolower($request->payment_method);
            $orderPayment->payment_channel = strtolower($request->payment_channel);
            $orderPayment->paid_at         = date('Y-m-d H:i:s', strtotime($request->paid_at));

            $orderPayment->save();

            if ($order->status != 'canceled') $order->status = $orderStatus;

            $order->save();

            (new OrderPaymentLogService)->store($order->id, 'xendit', $paymentStatus, $orderPayment->type);

            DB::commit();

            return $this->successResponse();
        } catch (\Exception $e) {
            return $this->errorServer($e->getMessage());
        }
    }
}
