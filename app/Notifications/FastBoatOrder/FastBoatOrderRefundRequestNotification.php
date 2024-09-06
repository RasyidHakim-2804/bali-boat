<?php

namespace App\Notifications\FastBoatOrder;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class FastBoatOrderRefundRequestNotification extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $lang = $this->data['language'];
        
        $subject    = ' - Fast Boat Order Refund';
        $message    = new HtmlString('Here we sent you the link refund for fast boat order with id <strong>' . $this->data['order_uuid'] . '</strong> and the secret password in separated email from Xendit. Please click "Refund" button tu redirect you to refund page. Thankyou');
        $button     = 'Refund';

        if ($lang == 'id') {
            $subject    = ' - Pengembalian Dana Order Fast Boat';
            $message    = new HtmlString('Berikut kami kirimkan link melakukan penarikan dana untuk order"Fast Boat" dengan nomor <strong>' . $this->data['order_uuid'] . '</strong> dan "secret password" melalui email terpisah dari Xendit. Klik tombol "Penarikan Dana" untuk meneruskan anda ke halaman refund. Terimakasih');
            $button     = 'Penarikan Dana';
        }

        return (new MailMessage)
            ->subject(env('APP_EMAIL_COMPANY') . $subject)
            ->greeting('Hi ' . $this->data['first_name'] . ',')
            ->line($message)
            ->action($button, url($this->data['refund_url']));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
