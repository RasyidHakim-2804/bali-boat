<?php

namespace App\Services\Email;

use App\Notifications\ActivityOrder\ActivityOrderExpiredNotification;
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

        $this->sendUserMail($data, $type, $user, $order->uuid);
        
        $dataAdmin = [];

        $adminEmail = env('MAIL_FROM_ADDRESS');

        
    }

    public function expired($order, $type)
    {
        $user = $order->user;

        $data               = [];
        $data['language']   = $user->language;
        $data['first_name'] = $user->first_name;

        $this->sendUserMail($data, $type, $user, $order->uuid);
    }

    protected function sendUserMail($data, $type, $user, $orderUuid) {
        if ($type == 'fast_boat') {
            $data['order_url']  = env('FRONTEND_URL') . '/user/orders/fast-boats/' . $orderUuid;

            $user->notify(new FastBoatOrderExpiredNotification($data));
        }

        if ($type == 'private_boat') {
            $data['order_url']  = env('FRONTEND_URL') . '/user/orders/private-boats/' . $orderUuid;

            $user->notify(new PrivateBoatOrderExpiredNotification($data));
        }

        if ($type == 'activity') {
            $data['order_url']  = env('FRONTEND_URL') . '/user/orders/tour/' . $orderUuid;

            $user->notify(new ActivityOrderExpiredNotification($data));
        }
    }

    protected function sendAdminMail($data, $type, $adminEmail, $orderUuid) {
        if ($type == 'fast_boat') {
            $data['order_url'] = env('FRONTEND_URL') . '/admin/orders/fast-boats/' . $orderUuid;

            Notification::route('mail', $adminEmail)->notify((new AdminFastBoatOrderPaidNotification($data)));
        }

        if ($type == 'private_boat') {
            $data['order_url'] = env('FRONTEND_URL') . '/admin/orders/private-boats/' . $orderUuid;

            Notification::route('mail', $adminEmail)->notify((new AdminPrivateBoatOrderPaidNotification($data)));
        }

        if ($type == 'activity') {
            $data['order_url'] = env('FRONTEND_URL') . '/admin/orders/tour/' . $orderUuid;

            Notification::route('mail', $adminEmail)->notify((new AdminActivityOrderPaidNotification($data)));
        }
    }
}