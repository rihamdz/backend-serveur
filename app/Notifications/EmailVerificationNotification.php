<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class EmailVerificationNotification extends Notification
{
    use Queueable;
    
    public function via($notifiable)
    {
        return ['mail'];
    }

    // public
     function toMail($notifiable)

    {       
        $url = route('api.verify-email', ['token' => $notifiable->verification_token]);

        return (new MailMessage)
            ->subject('Verify your email')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email', $url)
            ->line('If you did not create an account, no further action is required.');
    }

}
