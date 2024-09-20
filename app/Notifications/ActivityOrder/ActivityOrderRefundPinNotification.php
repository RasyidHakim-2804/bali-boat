<?php

namespace App\Notifications\ActivityOrder;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ActivityOrderRefundPinNotification extends Notification
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

        $subject    = ' - Activity Order Refund PIN';
        $message    = new HtmlString('Your refund request for fast boat order with id <strong>' . $this->data['order_uuid'] . '</strong> is almost done. Here we send you 6 digits PIN for the next step. The PIN is <strong>' . $this->data['pin'] . '</strong>.');
        $button     = 'Order Detail';

        if ($lang == 'id') {
            $subject    = ' - PIN Pengembalian Dana Order Activity';
            $message    = new HtmlString('Request refund anda untuk order "Activity" dengan nomor <strong>' . $this->data['order_uuid'] . '</strong> hampir selesai. Here we send you 6 digits PIN for the next step. The PIN is <strong>' . $this->data['pin'] . '</strong>.');
            $button     = 'Detil Order';
        }

        return (new MailMessage)
            ->subject(env('APP_EMAIL_COMPANY') . $subject)
            ->greeting('Hi ' . $this->data['first_name'] . ',')
            ->line($message);
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
