<?php

namespace App\Notifications\FastBoatOrder;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FastBoatOrderCreatedNotification extends Notification
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

        $subject    = ' - New Fast Boat Order';
        $button     = 'Payment';
        $line       = 'Your fast boat order was successfully created. Please complete the payment by clicking the "Payment" button. Thankyou.';

        if ($lang == 'id') {
            $subject    = ' - Order Fast Boat Baru';
            $button     = 'Pembayaran';
            $line       = 'Order "Fast Boat" anda berhasil terbuat. Mohon untuk menyelesaikan pembayaran dengan meng-klik tombol "Pembayaran". Terimakasih.';
        }

        return (new MailMessage)
            ->subject(env('APP_EMAIL_COMPANY') . $subject)
            ->greeting('Hi ' . $this->data['first_name'] . ',')
            ->line($line)
            ->action($button, url($this->data['payment_url']));
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
