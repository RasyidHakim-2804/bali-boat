<?php

namespace App\Services\Xendit;

use App\Interfaces\PaymentInterface;
use App\Models\OrderPayment;
use App\Models\XenditPayment;
use App\Services\Error\TelegramErrorNotificationService;
use App\Services\Logs\OrderPaymentLogService;
use Illuminate\Support\Facades\Log;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\XenditSdkException;

class XenditInvoiceService implements PaymentInterface
{
    protected $xendit;

    protected $invoiceApi;
    
    public function __construct()
    {
        $this->xendit       = Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
        $this->invoiceApi   = new InvoiceApi();
    }

    public function list()
    {
        try {
            $response = $this->invoiceApi->getInvoices();

            return $response;
        } catch (XenditSdkException $e) {
            Log::channel('xendit')->info($e->getMessage());

            (new TelegramErrorNotificationService)->send($e->getMessage() . ' in xendit invoice service file at list method');

            abort(500, $e->getMessage());
        }
    }

    public function show($invoiceId)
    {
        try {
            $response   = $this->invoiceApi->getInvoiceById($invoiceId);

            return $response;
        } catch (XenditSdkException $e) {
            Log::channel('xendit')->info($e->getMessage());

            (new TelegramErrorNotificationService)->send($e->getMessage() . ' in xendit invoice service file at show method');

            abort(500, $e->getMessage());
        }
    }

    public function pay($order, $type)
    {
        try {
            $data     = $this->generateRequestData($order);
            $response = $this->invoiceApi->createInvoice($data);

            $this->createXenditPayment($order, $type, $response);

            (new OrderPaymentLogService)->store($order->id, 'xendit', $response['status']);

            return $response;
        } catch (XenditSdkException $e) {
            Log::channel('xendit')->info($e->getMessage());

            (new TelegramErrorNotificationService)->send($e->getMessage() . ' in xendit invoice service file at pay method');

            abort(500, $e->getMessage());
        }
    }

    public function expire($order)
    {
        try {
            $invoiceId = $order->payment->invoice_id;
            $invoice   = $this->invoiceApi->getInvoiceById($invoiceId);

            if (strtolower($invoice['status']) != 'pending') return $invoice;

            $response  = $this->invoiceApi->expireInvoice($invoiceId);

            (new OrderPaymentLogService)->store($order->id, 'xendit', $response['status']);

            return $response;
        } catch (\Exception $e) {
            Log::channel('stripe')->info($e->getMessage());

            (new TelegramErrorNotificationService)->send($e->getMessage() . ' in CheckoutService file at expire method');

            abort(500, $e->getMessage());
        }
    }

    protected function generateRequestData($order)
    {
        $user = $order->user;
        
        $data = [
            "external_id"          => $order->uuid,
            "description"          => 'Bali Boat Invoice',
            "amount"               => intval($order->grand_total_purchased),
            "currency"             => "IDR",
            "success_redirect_url" => env('FRONTEND_URL') . '/payment-successfull?order=' . $order->uuid,
            "failed_redirect_url"  => env('FRONTEND_URL') . '/payment-failed?order=' . $order->uuid,
            "customer" => [
                'given_names'   => $user->first_name,
                'surname'       => $user->last_name,
                'email'         => $user->email,
                'mobile_number' => $user->first_name,
            ]
        ];

        if (!isset($user->mobile_number)) unset($data['customer']['mobile_number']);

        return $data;
    }

    public function createXenditPayment($order, $type, $xenditInvoice)
    {
        $invoice                      = new OrderPayment();
        $invoice->invoice_id          = $xenditInvoice['id'];
        $invoice->total_purchased     = $xenditInvoice['amount'];
        $invoice->status              = 'pending';
        $invoice->type                = $type;
        $invoice->url                 = $xenditInvoice['invoice_url'];
        $invoice->expired_at          = date('Y-m-d H:i:s', strtotime($order->created_at . ' +1 minutes'));

        if ($type == 'fast_boat') $invoice->fast_boat_order_id  = $order->id;

        $invoice->save();

        return $invoice;
    }
}