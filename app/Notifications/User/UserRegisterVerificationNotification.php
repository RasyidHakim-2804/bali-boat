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
        $line       = 'Your account has email changes request to this email. Please verify this email by click "Verify" button to make the email changes successful.';

        if ($lang == 'id') {
            $subject    = ' - Verifikasi Pendaftaran';
            $button     = 'Verifikasi';
            $line       = 'Anda melakukan permintaan untuk mengganti email akun anda ke email ini. Mohon melakukan verifikasi email dengan meng-klik tombol "Verifikasi" agar pergantian email berhasil.';
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
