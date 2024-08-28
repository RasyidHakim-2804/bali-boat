<?php

namespace App\Services\Xendit;

use App\Interfaces\PayoutInterface;
use App\Models\XenditPayoutLink;
use App\Services\Error\TelegramErrorNotificationService;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Xendit\Payout\PayoutApi;
use Xendit\XenditSdkException;

class XenditPayoutLinkService implements PayoutInterface
{
    protected $v1ApiUrl;

    protected $xenditToken;

    protected $payoutApi;
    
    public function __construct()
    {
        $this->v1ApiUrl     = env('XENDIT_API');
        $this->xenditToken  = base64_encode(env('XENDIT_SECRET_KEY') . ':');
        $this->payoutApi    = new PayoutApi();
    }

    public function show($payoutId)
    {
        try {
            $response   = (new Client())->request('GET', $this->v1ApiUrl . '/payouts/' . $payoutId, [
                'headers'   => [
                    'Authorization' => "Basic " . $this->xenditToken,
                ]
            ]);

            return json_decode($response->getBody());
        } catch (XenditSdkException $e) {
            (new TelegramErrorNotificationService)->send($e->getMessage() . ' in xendit payout link service file at show method');

            abort(500, $e->getMessage());
        }
    }

    public function payout($email, $referralId, $amount, $type)
    {
        try {
            $response   = (new Client())->request('POST', $this->v1ApiUrl . '/payouts', [
                'headers'   => [
                    'Authorization' => "Basic " . $this->xenditToken,
                ],
                'form_params' => [
                    'external_id'   => $referralId,
                    'amount'        => $amount,
                    'email'         => $email
                ]
            ]);

            $responseBody = json_decode($response->getBody());

            return $this->createXenditPayoutLink($responseBody, $type);
        } catch (XenditSdkException $e) {
            Log::channel('xendit')->info($e->getMessage());

            (new TelegramErrorNotificationService)->send($e->getMessage() . ' in xendit payout link service file at payout method');

            abort(500, $e->getMessage());
        }
    }

    public function cancel($payoutId)
    {
        try {
            $response   = (new Client())->request('POST', $this->v1ApiUrl . '/payouts/' . $payoutId . '/void', [
                'headers'   => [
                    'Authorization' => "Basic " . $this->xenditToken,
                ]
            ]);

            return json_decode($response->getBody());
        } catch (XenditSdkException $e) {
            (new TelegramErrorNotificationService)->send($e->getMessage() . ' in xendit payout link service file at cancel method');

            abort(500, $e->getMessage());
        }
    }
    
    public function createXenditPayoutLink($payoutLink, $type)
    {
        $xenditPayout                   = new XenditPayoutLink();
        $xenditPayout->payout_link_id   = $payoutLink->id;
        $xenditPayout->reference_id     = $payoutLink->external_id;
        $xenditPayout->amount           = $payoutLink->amount;
        $xenditPayout->payout_url       = $payoutLink->payout_url;
        $xenditPayout->expired_at       = Carbon::now()->addDays(3);
        $xenditPayout->status           = strtolower($payoutLink->status);
        $xenditPayout->type             = $type;

        $xenditPayout->save();

        return $xenditPayout;
    }
}