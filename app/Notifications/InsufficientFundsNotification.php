<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InsufficientFundsNotification extends Notification implements ShouldQueue
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

    /**
     * Send an email notification due to insufficient funds
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You Have Insufficient Funds')
            ->view('emails.insufficient_funds');
    }
}