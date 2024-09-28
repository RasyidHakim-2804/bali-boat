<?php

namespace App\Services\Email;

use App\Notifications\ActivityOrder\ActivityOrderRefundNotification;
use App\Notifications\FastBoatOrder\FastBoatOrderRefundNotification;
use App\Notifications\Order\OrderRefundNotification;
use App\Notifications\PrivateBoatOrder\PrivateBoatOrderRefundNotification;
use Illuminate\Support\Facades\Notification;

class OrderRefundEmailService 
{
    public function success($order, $type)
    {
        $user = $order->user;

        $data               = [];
        $data['first_name'] = $user->first_name;
        $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;
        $data['message']    = 'Your order has been refunded. The process will take 5 - 10 days. Thank you for your compassion';

        if ($user->language == 'id') $data['message'] = 'Pengembalian dana pada pesanan anda telah diproses. Proses akan selesai dalam 5 - 10 hari. Terimakasih.';

        if ($type == 'fast_boat') $user->notify(new FastBoatOrderRefundNotification($data));

        if ($type == 'private_boat') $user->notify(new PrivateBoatOrderRefundNotification($data));

        if ($type == 'activity') $user->notify(new ActivityOrderRefundNotification($data));
    }

    public function pending($order, $type)
    {
        $user = $order->user;

        $data               = [];
        $data['first_name'] = $user->first_name;
        $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;
        $data['message']    = 'The status of refunding process of your order is pending. Need a confirmation from your side to make the refund success.';

        if ($user->language == 'id') $data['message'] = 'Permintaan request anda sedang berlangsung. Butuh konfirmasi dari anda untuk menyelesaikan proses refund. Terimakasih.';

        if ($type == 'fast_boat') $user->notify(new FastBoatOrderRefundNotification($data));

        if ($type == 'private_boat') $user->notify(new PrivateBoatOrderRefundNotification($data));

        if ($type == 'activity') $user->notify(new ActivityOrderRefundNotification($data));
    }

    public function failed($order, $type, $message)
    {
        $user = $order->user;

        $data               = [];
        $data['first_name'] = $user->first_name;
        $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;
        $data['message']    = $message;

        if ($type == 'fast_boat') $user->notify(new FastBoatOrderRefundNotification($data));

        if ($type == 'private_boat') $user->notify(new PrivateBoatOrderRefundNotification($data));

        if ($type == 'activity') $user->notify(new ActivityOrderRefundNotification($data));
    }

    public function failedForAdmin($order, $type)
    {
        $email = env('MAIL_FROM_ADDRESS');

        $data               = [];
        $data['first_name'] = 'Admin';
        $data['message']    = 'Your xendit balance is insufficient for this order. Please click "Order Detail" button for more information. Thankyou.';
        $data['order_url']  = env('FRONTEND_URL') . '/admin/orders/' . $order->uuid;

        if ($type == 'fast_boat') Notification::route('mail', $email)->notify(new FastBoatOrderRefundNotification($data));

        if ($type == 'private_boat') Notification::route('mail', $email)->notify(new PrivateBoatOrderRefundNotification($data));

        if ($type == 'activity') Notification::route('mail', $email)->notify(new ActivityOrderRefundNotification($data));
    }

    public function expired($order, $type)
    {
        $user = $order->user;

        $data               = [];
        $data['first_name'] = $user->first_name;
        $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;
        $data['message']    = 'Your refund request is expired and the refund link that we sent to your email is not valid anymore. You can make a new refund request by clicking "Order Detail" button and then click "Refund" button. Thankyou.';

        if ($user->language == 'id') $data['message'] = 'Permintaan refund anda telah kadaluarsa dan link refund yang telah dikirim ke email anda tidak dapat digunakan lagi. Anda dapat melakukan request untuk refund kembali dengan mngklik tombol "Order Detail" lalu mengklik tombol "Refund". Terimakasih.';

        if ($type == 'fast_boat') $user->notify(new FastBoatOrderRefundNotification($data));

        if ($type == 'private_boat') $user->notify(new PrivateBoatOrderRefundNotification($data));

        if ($type == 'activity') $user->notify(new ActivityOrderRefundNotification($data));
    }
}