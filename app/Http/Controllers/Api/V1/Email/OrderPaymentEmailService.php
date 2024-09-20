<?php

namespace App\Services\Email;

use App\Notifications\ActivityOrder\AdminActivityOrderPaidNotification;
use App\Notifications\FastBoatOrder\AdminFastBoatOrderPaidNotification;
use App\Notifications\FastBoatOrder\FastBoatOrderExpiredNotification;
use App\Notifications\FastBoatOrder\FastBoatOrderPaidNotification;
use App\Notifications\PrivateBoatOrder\AdminPrivateBoatOrderPaidNotification;
use App\Notifications\PrivateBoatOrder\PrivateBoatOrderExpiredNotification;
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
            $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;

            $user->notify(new FastBoatOrderPaidNotification($data));
        }
        
        $dataAdmin = [];

        $adminEmail = env('MAIL_FROM_ADDRESS');

        if ($type == 'fast_boat') {
            Notification::route('mail', $adminEmail)->notify((new AdminFastBoatOrderPaidNotification($dataAdmin)));

            $dataAdmin['order_url'] = env('FRONTEND_URL') . '/admin/fast-boat-orders/' . $order->uuid . '/detail';
        }

        if ($type == 'private_boat') {
            Notification::route('mail', $adminEmail)->notify((new AdminPrivateBoatOrderPaidNotification($dataAdmin)));

            $dataAdmin['order_url'] = env('FRONTEND_URL') . '/admin/private-boat-orders/' . $order->uuid . '/detail';
        }

        if ($type == 'activity') {
            Notification::route('mail', $adminEmail)->notify((new AdminActivityOrderPaidNotification($dataAdmin)));

            $dataAdmin['order_url'] = env('FRONTEND_URL') . '/admin/activity-orders/' . $order->uuid . '/detail';
        }
    }

    public function expired($order, $type)
    {
        $user = $order->user;

        $data               = [];
        $data['language']   = $user->language;
        $data['first_name'] = $user->first_name;

        if ($type == 'fast_boat') {
            $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;

            $user->notify(new FastBoatOrderExpiredNotification($data));
        }

        if ($type == 'private_boat') {
            $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;

            $user->notify(new PrivateBoatOrderExpiredNotification($data));
        }
    }
}