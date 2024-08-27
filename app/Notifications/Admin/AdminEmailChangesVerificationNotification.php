<?php

namespace App\Notifications\Admin;

use App\Traits\NotificationHasTranslationsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminEmailChangesVerificationNotification extends Notification
{
    use Queueable, NotificationHasTranslationsTrait;

    private $translations = [
        'en' => [
            'line_1' => 'Your admin account has email changes request to this email. Please verify this email by click \'Verify\' button to make the email changes successful.',
            'button' => 'Verify'
        ],

        'id' => [
            'line_1' => 'Akun admin Anda memiliki permintaan perubahan email ke email ini. Silakan verifikasi email ini dengan mengklik tombol \'Verifikasi\' agar perubahan email berhasil.',
            'button' => 'Verifikasi'
        ]
    ];

    private $verifyUrl;
    private $firstName;

    public function __construct($url, $firstName)
    {
        $this->verifyUrl = $url;
        $this->firstName = $firstName;
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
                    ->subject(env('APP_EMAIL_COMPANY') . ' - Admin Email Changes')
                    ->greeting('Hi ' . $this->firstName . ',')
                    ->line($this->getTranslations('line_1'))
                    ->action($this->getTranslations('button'), url('/'));
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
