<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminRegisterVerificationNotification extends Notification
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

        $subject    = ' - Admin Registration';
        $line       = 'Your admin account registration almost done. Please click verify button to verify your email.';
        $button     = 'Verify';

        if ($lang == 'id') {
            $subject    = ' - Pendaftaran Admin';
            $line       = 'Pendaftaran akun admin Anda hampir selesai. Silakan klik tombol verifikasi untuk memverifikasi email Anda.';
            $button     = 'Verifikasi';
        }

        return (new MailMessage)
                    ->subject(env('APP_EMAIL_COMPANY') . $subject)
                    ->greeting('Hi ' . $this->data['first_name'] . ',')
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
