<?php

namespace App\Traits;

use App\Services\Telegram\TelegramErrorNotificationService;
use Exception;
use Illuminate\Http\Response;

Trait JsonResponsesTrait
{
    public function successResponse()
    {
        return response()->json([
            'status' => 'success',
        ], Response::HTTP_ACCEPTED);
    }

    public function successResponseMessage($message)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
        ], Response::HTTP_ACCEPTED);
    }

    public function successGet($data)
    {
        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ], Response::HTTP_OK);
    }

    public function successStore()
    {
        return response()->json([
            'status' => 'success',
        ], Response::HTTP_CREATED);
    }

    public function successStoreWithData($data)
    {
        return response()->json([
            'status' => 'success',
            'data'   => $data
        ], Response::HTTP_CREATED);
    }

    public function successStoreResponse($message)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
        ], Response::HTTP_CREATED);
    }

    public function errorStore()
    {
        return response()->json([
            'status' => 'fail',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function errorMessage($message = 'Failed to process', $code = null)
    {
        (new TelegramErrorNotificationService)->send($message);

        if (!isset($code)) $code = Response::HTTP_UNPROCESSABLE_ENTITY; 

        if (request()->wantsJson()) {
            return response()->json([
                'status'    => 'fail',
                'message'   => $message,
            ], $code);
        }
    }

    public function errorServer($message = 'Server Error')
    {
        (new TelegramErrorNotificationService)->send($message);

        return response()->json([
            'status' => 'fail',
            'message'   => $message,
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function errorValidationResponse($key, $message)
    {
        return response()->json([
            'message' => $message,
            'errors'  => [
                $key => [$message]
            ]
        ], 422);
    }

    public function errorUnauthorized()
    {
        return response()->json([
            'message' => 'Unauthorized.',
        ], 401);
    }

    public function unauthtenticate()
    {
        return response()->json([
            'status'  => 'fail',
            'message' => 'Unauthenticate!'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function loginTokenResponse($token, $role, $message = 'success')
    {
        return response()->json([
            'message' => $message,
            'token'   => $token,
            'role'    => $role
        ], 200);
    }
}