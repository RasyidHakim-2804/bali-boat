<?php

namespace App\Notifications\Admin;

use App\Traits\NotificationHasTranslationsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgetAdminPasswordNotification extends Notification
{
    use Queueable, NotificationHasTranslationsTrait;

    private $translations = [
        'en' => [
            'line_1' => 'Your forget password request for admin account is success. Here we send you 6 digits PIN for the next step.'
        ],
        'id' => [
            'line_1' => 'Permintaan lupa kata sandi untuk akun admin Anda berhasil. Di sini kami mengirimkan 6 digit PIN untuk langkah selanjutnya.'
        ],
    ];

    public function __construct(private string $name, private string $pin)
    {
        //
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
                    ->subject('Admin Forget Password Pin')
                    ->greeting('Hi ' . $this->name . ',')
                    ->line($this->getTranslations('line_1'))
                    ->line('# ' . $this->pin);
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
