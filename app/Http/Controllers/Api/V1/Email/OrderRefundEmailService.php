<?php

namespace App\Services\Email;

use App\Notifications\Order\OrderRefundNotification;
use Illuminate\Support\Facades\Notification;

class OrderRefundEmailService 
{
    public function success($order)
    {
        $user = $order->user;

        $data               = [];
        $data['first_name'] = $user->first_name;
        $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;
        $data['message']    = 'Your order has been refunded. The process will take 5 - 10 days. Thank you for your compassion';

        if ($user->language == 'id') $data['message'] = 'Pengembalian dana pada pesanan anda telah diproses. Proses akan selesai dalam 5 - 10 hari. Terimakasih.';

        $user->notify(new OrderRefundNotification($data));
    }

    public function pending($order)
    {
        $user = $order->user;

        $data               = [];
        $data['first_name'] = $user->first_name;
        $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;
        $data['message']    = 'The status of refunding process of your order is pending. Need a confirmation from your side to make the refund success.';

        if ($user->language == 'id') $data['message'] = 'Permintaan request anda sedang berlangsung. Butuh konfirmasi dari anda untuk menyelesaikan proses refund. Terimakasih.';

        $user->notify(new OrderRefundNotification($data));
    }

    public function failed($order, $message)
    {
        $user = $order->user;

        $data               = [];
        $data['first_name'] = $user->first_name;
        $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;
        $data['message']    = $message;

        $user->notify(new OrderRefundNotification($data));
    }

    public function failedForAdmin($order)
    {
        $email = env('MAIL_FROM_ADDRESS');

        $data               = [];
        $data['first_name'] = 'Admin';
        $data['message']    = 'Your xendit balance is insufficient for this order. Please click "Order Detail" button for more information. Thankyou.';
        $data['order_url']  = env('FRONTEND_URL') . '/admin/orders/' . $order->uuid;

        Notification::route('mail', $email)->notify(new OrderRefundNotification($data));
    }

    public function expired($order)
    {
        $user = $order->user;

        $data               = [];
        $data['first_name'] = $user->first_name;
        $data['order_url']  = env('FRONTEND_URL') . '/user/orders/' . $order->uuid;
        $data['message']    = 'Your refund request is expired and the refund link that we sent to your email is not valid anymore. You can make a new refund request by clicking "Order Detail" button and then click "Refund" button. Thankyou.';

        if ($user->language == 'id') $data['message'] = 'Permintaan refund anda telah kadaluarsa dan link refund yang telah dikirim ke email anda tidak dapat digunakan lagi. Anda dapat melakukan request untuk refund kembali dengan mngklik tombol "Order Detail" lalu mengklik tombol "Refund". Terimakasih.';

        $user->notify(new OrderRefundNotification($data));
    }
}