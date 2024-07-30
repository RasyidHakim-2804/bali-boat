<?php

namespace App\Traits;

use App\Services\Error\TelegramErrorNotificationService;
use Exception;
use Illuminate\Http\Response;

Trait ArrayResponsesTrait
{
    public function success($message = null)
    {
        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => $message
        ];
    }

    public function successData($data)
    {
        return [
            'status' => 'success',
            'code'   => 200,
            'data'   => $data
        ];
    }

    public function fail($message, $file = null, $line = null, $method = null)
    {
        (new TelegramErrorNotificationService)->send($message . ' in ' . $file . ' file at ' . $method . 'method.');

        return [
            'status'  => 'fail',
            'code'    => 500,
            'message' => $message
        ];
    }
}