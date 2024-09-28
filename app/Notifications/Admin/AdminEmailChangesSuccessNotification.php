<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminEmailChangesSuccessNotification extends Notification
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

        $subject    = ' - Admin Email Changes Success';
        $line       = 'Your email changes request for admin account to this email is success. For the next email changes will be available after 1 month from last email changes. Thankyou.';

        if ($lang == 'id') {
            $subject    = ' - Perubahan Email Admin Berhasil';
            $line       = 'Perubahan email untuk akun admin anda berhasil. Untuk perubahan email anda akan tersedia setelah 1 bulan dari perubahan email ana yang terakhir. Terimakasih.';
        }

        return (new MailMessage)
                    ->subject(env('APP_EMAIL_COMPANY') . $subject)
                    ->greeting('Hi' . $this->data['first_admin'])
                    ->line($line);
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
