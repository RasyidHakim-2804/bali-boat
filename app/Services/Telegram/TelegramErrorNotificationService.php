<?php

namespace App\Services\Telegram;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TelegramErrorNotificationService
{
    public function send($message)
    {
        $request = request();

        try {
            $text  = env('APP_NAME') . ' ' . env('APP_ENV') . ' errors' . PHP_EOL . PHP_EOL; 
            $text .= 'Method : ' . PHP_EOL . $request->method() . PHP_EOL . PHP_EOL;
            $text .= 'Url : ' . PHP_EOL . $request->url() . PHP_EOL . PHP_EOL;
            $text .= 'Error : ' . PHP_EOL . $message;

            $endpoint = 'https://api.telegram.org/bot6674688859:AAEb17vTITTYZiVtVYn7qyxnbzS_xrFitDI/sendMessage';
            
            (new Client())->request('POST', $endpoint, [
                'query' => [
                    'chat_id' => '-999839774',
                    'text'    => $text
                ]
            ]);

            return true;

        } catch (\Exception $e) {
            Log::info('Error - Telegram Error Notification Service');
            Log::info($e);

            return $e->getMessage();
        }
    }
}