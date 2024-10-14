<?php

namespace App\Notifications\User;

use App\Traits\NotificationHasTranslationsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegisterVerificationNotification extends Notification
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

        $subject    = ' - User Registration Verification';
        $button     = 'Verify';
        $line       = 'Your account registration almost done. Please click "Verify" button to verify your email';

        if ($lang == 'id') {
            $subject    = ' - Verifikasi Pendaftaran';
            $button     = 'Verifikasi';
            $line       = 'Pendaftaran akun anda hampir selesai. Mohon klik tombol "Verifikasi" untuk memverifikasi email anda.';
        }

        return (new MailMessage)
            ->subject(env('APP_EMAIL_COMPANY') . $subject)
            ->greeting('Hi ' . $this->data['first_name'] . ',')
            ->line($line)
            ->action($button, url($this->data['email_verification_url']));
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
