<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegisterVerificationEmail extends Notification
{
    use Queueable;

    protected $name;

    protected $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($name, $token)
    {
        $this->name  = $name;
        $this->token = $token;
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
        $url = 'https://auth-app.com/user/email-verification/' . $this->token;

        return (new MailMessage)
            ->subject('AUTH APP - Email User Registration Verification')
            ->greeting('Hi ' . $this->name . ',')
            ->line('This email has successfully registered to AUTH APP Talent Registration. Please complete the registration by click "confirm" button as email verification.')
            ->action('CONFIRM', $url);
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
