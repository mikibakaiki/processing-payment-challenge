<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentDelayedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $isPromoter;

    public function __construct(bool $isPromoter)
    {
        //
        $this->isPromoter = $isPromoter;
    }


    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Send an email notification due to delayed payment
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Payment Is Delayed')
            ->view('emails.delayed_payment', ['isPromoter' => $this->isPromoter]);
    }
}