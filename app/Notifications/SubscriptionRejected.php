<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionRejected extends Notification
{
    use Queueable;

    public function __construct(public UserSubscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Subscription Payment Rejected - ' . get_setting('site_name'))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Unfortunately, your bank transfer payment could not be verified.')
            ->line('**Plan:** ' . ($this->subscription->plan->title ?? 'N/A'))
            ->line('**Transaction ID:** ' . $this->subscription->transaction_id)
            ->when($this->subscription->admin_note, fn($mail) => $mail->line('**Reason:** ' . $this->subscription->admin_note))
            ->action('Try Again', route('pricing.plans'))
            ->line('Please contact support if you believe this is an error.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your subscription payment for "' . ($this->subscription->plan->title ?? 'N/A') . '" has been rejected. Please contact support.',
            'link'    => route('pricing.plans'),
        ];
    }
}
