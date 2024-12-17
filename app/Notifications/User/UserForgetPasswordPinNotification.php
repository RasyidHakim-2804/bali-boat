<?php

namespace App\Notifications\User;

use App\Traits\NotificationHasTranslationsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class UserForgetPasswordPinNotification extends Notification
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

        $subject    = ' - Forgot Password PIN';
        $line       = new HtmlString('Your forget password request is success. Here we send you 6 digits PIN for next step. The PIN is <strong>' . $this->data['pin'] . '</strong>.');

        if ($lang == 'id') {
            $subject    = ' - PIN Lupa Password';
            $line       = new HtmlString('Anda melakukan permintaan pergantian kata sandi. Berikut 6 dikit PIN untuk melanjutkan ke tahap berikutnya. PIN tersebut yaitu <strong>' . $this->data['pin'] . '</strong>.');
        }

        return (new MailMessage)
            ->subject(env('APP_EMAIL_COMPANY') . $subject)
            ->greeting('Hi ' . $this->data['first_name'] . ',')
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
