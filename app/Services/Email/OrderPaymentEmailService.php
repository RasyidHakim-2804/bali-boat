<?php

namespace App\Services\Email;

use App\Notifications\ActivityOrder\ActivityOrderExpiredNotification;
use App\Notifications\ActivityOrder\ActivityOrderPaidNotification;
use App\Notifications\ActivityOrder\AdminActivityOrderPaidNotification;
use App\Notifications\FastBoatOrder\AdminFastBoatOrderPaidNotification;
use App\Notifications\FastBoatOrder\FastBoatOrderExpiredNotification;
use App\Notifications\FastBoatOrder\FastBoatOrderPaidNotification;
use App\Notifications\PrivateBoatOrder\AdminPrivateBoatOrderPaidNotification;
use App\Notifications\PrivateBoatOrder\PrivateBoatOrderExpiredNotification;
use App\Notifications\PrivateBoatOrder\PrivateBoatOrderPaidNotification;
use Illuminate\Support\Facades\Notification;

class OrderPaymentEmailService 
{
    public function paid($order, $type)
    {
        $user = $order->user;

        $data               = [];
        $data['language']   = $user->language;
        $data['first_name'] = $user->first_name;

        if ($type == 'fast_boat') {
            $data['order_url']  = env('FRONTEND_URL') . '/user/orders/fast-boats/' . $order->uuid;

            if (!$user->email_verified_at)  $data['order_url']  = env('FRONTEND_URL') . '/orders/fast-boats/' . $order->uuid;

            $user->notify(new FastBoatOrderPaidNotification($data));
        }

        if ($type == 'private_boat') {
            $data['order_url']  = env('FRONTEND_URL') . '/user/orders/private-boats/' . $order->uuid;

            if (!$user->email_verified_at)  $data['order_url']  = env('FRONTEND_URL') . '/orders/private-boats/' . $order->uuid;

            $user->notify(new PrivateBoatOrderPaidNotification($data));
        }

        if ($type == 'activity') {
            $data['order_url']  = env('FRONTEND_URL') . '/user/orders/tour/' . $order->uuid;

            if (!$user->email_verified_at)  $data['order_url']  = env('FRONTEND_URL') . '/orders/tour/' . $order->uuid;

            $user->notify(new ActivityOrderPaidNotification($data));
        }
        
        $dataAdmin                  = [];
        $dataAdmin['language']      = 'en';
        $dataAdmin['first_name']    = 'Admin';
        $adminEmail = env('MAIL_FROM_ADDRESS');

        if ($type == 'fast_boat') {
            $dataAdmin['order_url'] = env('FRONTEND_URL') . '/admin/orders/fast-boats/' . $order->uuid;

            Notification::route('mail', $adminEmail)->notify((new AdminFastBoatOrderPaidNotification($dataAdmin)));
        }

        if ($type == 'private_boat') {
            $dataAdmin['order_url'] = env('FRONTEND_URL') . '/admin/orders/private-boats/' . $order->uuid;

            Notification::route('mail', $adminEmail)->notify((new AdminPrivateBoatOrderPaidNotification($dataAdmin)));
        }

        if ($type == 'activity') {
            $dataAdmin['order_url'] = env('FRONTEND_URL') . '/admin/orders/tour/' . $order->uuid;

            Notification::route('mail', $adminEmail)->notify((new AdminActivityOrderPaidNotification($dataAdmin)));
        }
    }

    public function expired($order, $type)
    {
        $user = $order->user;

        $data               = [];
        $data['language']   = $user->language;
        $data['first_name'] = $user->first_name;

        if ($type == 'fast_boat') {
            $data['order_url']  = env('FRONTEND_URL') . '/user/orders/fast-boats/' . $order->uuid;

            if (!$user->email_verified_at)  $data['order_url']  = env('FRONTEND_URL') . '/orders/fast-boats/' . $order->uuid;

            $user->notify(new FastBoatOrderExpiredNotification($data));
        }

        if ($type == 'private_boat') {
            $data['order_url']  = env('FRONTEND_URL') . '/user/orders/private-boats/' . $order->uuid;

            if (!$user->email_verified_at)  $data['order_url']  = env('FRONTEND_URL') . '/orders/private-boats/' . $order->uuid;

            $user->notify(new PrivateBoatOrderExpiredNotification($data));
        }

        if ($type == 'activity') {
            $data['order_url']  = env('FRONTEND_URL') . '/user/orders/tour/' . $order->uuid;

            if (!$user->email_verified_at)  $data['order_url']  = env('FRONTEND_URL') . '/orders/tour/' . $order->uuid;

            $user->notify(new ActivityOrderExpiredNotification($data));
        }
    }
}