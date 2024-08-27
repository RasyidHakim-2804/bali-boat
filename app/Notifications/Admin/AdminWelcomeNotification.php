<?php

namespace App\Notifications\Admin;

use App\Traits\NotificationHasTranslationsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminWelcomeNotification extends Notification
{
    use Queueable, NotificationHasTranslationsTrait;

    private $translations = [
        'en' => [
            'line_1' => 'We warmly welcome you as an admin to our eCommerce platform, and you are just one step away from creating your first move by clicking the "Login" button.'
        ],
        'id' => [
            'line_1' => 'Kami menyambut Anda dengan hangat sebagai admin di platform eCommerce kami, dan Anda tinggal selangkah lagi untuk melakukan langkah pertama dengan mengklik tombol “Login”.'
        ],
    ];

    private $firstName;
    private $loginUrl;
    
    public function __construct($firstName, $loginUrl)
    {
        $this->firstName = $firstName;
        $this->loginUrl  = $loginUrl;
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
        return (new MailMessage)
                    ->subject(env('APP_EMAIL_COMPANY') . ' - Welcome to Bali Boat')
                    ->line($this->getTranslations('line_1'))
                    ->action('Login', $this->loginUrl);
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
