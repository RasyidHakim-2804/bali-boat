<?php

namespace App\Notifications\Admin;

use App\Traits\NotificationHasTranslationsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminWelcomeNotification extends Notification
{
    use Queueable;

    private $data;

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

        $subject    = ' - Welcome to Bali Boat';
        $line       = 'We warmly welcome you as an admin to our eCommerce platform, and you are just one step away from creating your first move by clicking the "Login" button.';

        if ($lang == 'id') {
            $subject    = ' - Selamat Datang di Bali Boat';
            $line       = 'Kami menyambut Anda dengan hangat sebagai admin di platform eCommerce kami, dan Anda tinggal selangkah lagi untuk melakukan langkah pertama dengan mengklik tombol “Login”.';
        }
       
        return (new MailMessage)
                    ->subject(env('APP_EMAIL_COMPANY') . $subject)
                    ->line($line)
                    ->action('Login', $this->data['login_page_url']);
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
