<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Controllers\Controller;
use App\Models\StripeEventLog;
use App\Services\Stripe\Webhook\ChargeDisputeWebhookService;
use App\Services\Stripe\Webhook\CheckoutWebhookService;
use App\Services\Stripe\Webhook\PaymentIntentWebhookService;
use App\Services\Stripe\Webhook\RefundWebhookService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookOrderController extends Controller
{
    use JsonResponsesTrait;

    public function __invoke()
    {
        Log::channel('stripe')->info('');
        Log::channel('stripe')->info('New Event Log');
        
        \Stripe\Stripe::setApiKey('STRIPE_SECRET_KEY');

        $stripeEventLog = new StripeEventLog();

        $endpoint_secret = env('STRIPE_SIGNATURE_SECRET');
        $payload         = @file_get_contents('php://input');
        $event           = null;
        $sig_header      = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        try {
            $event = \Stripe\Webhook::constructEvent(
              $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            $stripeEventLog->exception = 'Invalid payload';

            Log::channel('stripe')->info('Invalid payload');

            $result = ['status' => 'fail', 'code' => 422, 'message' => 'Invalid payload.'];

            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            $stripeEventLog->exception = 'Invalid signature';

            Log::channel('stripe')->info('Invalid signature');

            $result = ['status' => 'fail', 'code' => 422, 'message' => 'Invalid signature.'];

            exit();
        }

        try {
            $payloadData = $event->data->object;

            $stripeEventLog->payload           = json_encode($payloadData);
            $stripeEventLog->event             = $event->type;
            $stripeEventLog->created_at        = now();
            $stripeEventLog->checkout_id       = 'unidentified';
            $stripeEventLog->paymentintent_id  = 'unidentified';

            if ($payloadData->object == 'checkout.session') $stripeEventLog->checkout_id = $payloadData->id;

            if ($payloadData->object == 'payment_intent') $stripeEventLog->paymentintent_id = $payloadData->id;

            $stripeEventLog->save();

            // Handle the event
            switch ($event->type) {
                case 'checkout.session.expired':
                    $result = (new CheckoutWebhookService)->expired($payloadData);

                    Log::channel('stripe')->info('Webhook checkout expired.');

                    break;
                case 'checkout.session.completed':
                    $result = (new CheckoutWebhookService)->complete($payloadData);

                    Log::channel('stripe')->info('Webhook checkout success.');

                    break;
                case 'payment_intent.canceled':
                    $result = (new PaymentIntentWebhookService)->canceled($payloadData->id);

                    Log::channel('stripe')->info('Webhook payment intent canceled.');

                    break;
                case 'payment_intent.created':
                    Log::channel('stripe')->info('Webhook payment intent created.');

                    break;
                case 'payment_intent.payment_failed':
                    $result = (new PaymentIntentWebhookService)->failed($payloadData->id);

                    Log::channel('stripe')->info('Webhook payment intent failed.');
                    
                    break;
                case 'payment_intent.processing':
                    $result = (new PaymentIntentWebhookService)->processing($payloadData->id);

                    Log::channel('stripe')->info('Webhook payment intent processing.');

                    break;
                case 'payment_intent.succeeded':
                    $result = (new PaymentIntentWebhookService)->success($payloadData->id);

                    Log::channel('stripe')->info('Webhook payment intent success.');

                    break;
                case 'checkout.session.async_payment_failed':
                    Log::channel('stripe')->info('Webhook checkout async payment failed.');

                    break;
                case 'checkout.session.async_payment_succeeded':
                    Log::channel('stripe')->info('Webhook checkout async payment success.');

                    break;
                case 'refund.updated';
                    $result = (new RefundWebhookService)->update($payloadData);

                    Log::channel('stripe')->info('Webhook refund updated.');

                    break;
                case 'charge.dispute.created';
                    $result = (new ChargeDisputeWebhookService)->created($payloadData);

                    Log::channel('stripe')->info('Webhook checkout async payment success.');

                    break;
                default:
                    echo 'Received unknown event type ' . $event->type;

                    Log::channel('stripe')->info('Webhook received unknown event type. The type is ' . $event->type);

                    $result = ['status' => 'fail', 'code' => 422, 'message' => 'Webhook received unknown event type. The type is ' . $event->type];

                    break;
            }

            if ($result['status'] == 'fail') return $this->errorMessage($result['message'], $result['code']);

            return $this->successResponseMessage($result['message']);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage());
        }
    }
}
