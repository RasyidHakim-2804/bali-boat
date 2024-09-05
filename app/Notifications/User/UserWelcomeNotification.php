<?php

namespace App\Notifications\User;

use App\Traits\NotificationHasTranslationsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserWelcomeNotification extends Notification
{
    use Queueable, NotificationHasTranslationsTrait;

    protected $data;

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

        $subject    = ' - Welcome';
        $button     = 'Login';
        $line       = 'We warmly welcome you to our eCommerce platform, and you are just one step away from creating your first order by clicking the "Login" button.';

        if ($lang == 'id') {
            $subject    = ' - Selamat Datang';
            $button     = 'Login';
            $line       = 'Selamat datang dan selamat bergabung ke website kami, dan anda hanya butuh satu langkah lagi untuk dapat mebuat order pertama anda dengan meng-klik tombol "Login". Terimakasih.';
        }

        return (new MailMessage)
            ->subject(env('APP_EMAIL_COMPANY') . $subject)
            ->greeting('Hi ' . $this->data['first_name'] . ',')
            ->line($line)
            ->action($button, url($this->data['login_page_url']));
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
