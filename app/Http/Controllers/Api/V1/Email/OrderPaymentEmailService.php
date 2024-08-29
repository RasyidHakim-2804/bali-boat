<?php

namespace App\Services\Email;

use App\Notifications\Order\AdminOrderPaidNotification;
use App\Notifications\Order\OrderExpiredNotification;
use App\Notifications\Order\OrderPaidNotification;
use Illuminate\Support\Facades\Notification;

class OrderPaymentEmailService 
{
    public function paid($order)
    {
        $user = $order->user;

        $data               = [];
        $data['language']   = $user->language;
        $data['first_name'] = $user->first_name;
        $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;

        $user->notify(new OrderPaidNotification($data));

        
        $dataAdmin              = [];
        $dataAdmin['order_url'] = env('FRONTEND_URL') . '/admin/orders/' . $order->uuid . '/detail';

        $adminEmail = env('MAIL_FROM_ADDRESS');

        Notification::route('mail', $adminEmail)->notify((new AdminOrderPaidNotification($dataAdmin)));
    }

    public function expired($order)
    {
        $user = $order->user;

        $data               = [];
        $data['language']   = $user->language;
        $data['first_name'] = $user->first_name;
        $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;

        $user->notify(new OrderExpiredNotification($data));
    }
}