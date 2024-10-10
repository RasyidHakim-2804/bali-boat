<?php

namespace App\Notifications\PrivateBoatOrder;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrivateBoatOrderCreatedNotification extends Notification
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

        $subject    = ' - New Private Boat Inquiry';
        $button     = 'Inquiry Detail';
        $line       = 'Your private boat inquiry was successfully created. We will process your order inquiry as soon as possible. Thankyou.';

        if ($lang == 'id') {
            $subject    = ' - Permintaan Private Boat Baru';
            $button     = 'Detil Permintaan';
            $line       = 'Permintaan "Private Boat" anda berhasil terbuat. Kami akan memproses pesanan anda segera. Terimakasih.';
        }

        return (new MailMessage)
            ->subject(env('APP_EMAIL_COMPANY') . $subject)
            ->greeting('Hi ' . $this->data['first_name'] . ',')
            ->line($line)
            ->action($button, url($this->data['order_url']));
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
