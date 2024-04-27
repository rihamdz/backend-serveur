<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url("/verify-email/{$notifiable->verification_token}");
    
        // Output the URL for debugging
        info("Verification URL: $url");
    
        return (new MailMessage)
            ->subject('Verify your email')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email', $url)
            ->line('If you did not create an account, no further action is required.');
    }
    

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
