<?php

namespace App\Services\Email;

use App\Notifications\FastBoatOrder\AdminFastBoatOrderPaidNotification;
use App\Notifications\FastBoatOrder\FastBoatOrderExpiredNotification;
use App\Notifications\FastBoatOrder\FastBoatOrderPaidNotification;
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

            $dataAdmin['order_url'] = env('FRONTEND_URL') . '/admin/orders/' . $order->uuid . '/detail';
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
    }
}