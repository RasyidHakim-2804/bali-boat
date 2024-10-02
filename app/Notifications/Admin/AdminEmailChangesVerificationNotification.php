<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminEmailChangesVerificationNotification extends Notification
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

        $subject    = ' - Admin Email Changes';
        $line       = 'Your admin account has email changes request to this email. Please verify this email by click "Verify" button to make the email changes successful.';
        $button     = 'Verify';

        if ($lang == 'id') {
            $subject    = ' - Perubahan Email Admin';
            $line       = 'Akun admin Anda memiliki permintaan perubahan email ke email ini. Silakan verifikasi email ini dengan mengklik tombol "Verifikasi" agar perubahan email berhasil.';
            $button     = 'Verifikasi';
        }

        return (new MailMessage)
                    ->subject(env('APP_EMAIL_COMPANY') . $subject)
                    ->greeting('Hi ' . $this->data['firstName'] . ',')
                    ->line($line)
                    ->action($button, $this->data['email_validation_url']);
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
