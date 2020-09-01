<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    use Queueable;

    
    public function toMail($notifiable)
    {
        //config maps to the client (nuxt) url we set up in the config folder with the appending of the
        //route we set up in the api routes file (/password/reset/) and the token we get from the user model calling this notification and the query string of the email of the person who is requesting the reset
        $url = url(config('app.client_url') . '/password/reset/' . $this->token . '?email=' . urlencode($notifiable->email));
        return (new MailMessage)
                    ->line('You are receiving this email because we had a password reset request sent for your account')
                    ->action('Reset Password', $url)
                    ->line('If you did not request this reset, no further action is required');
    }
   
}
