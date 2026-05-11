<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;

    public function __construct(public UserSubscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysLeft = now()->diffInDays($this->subscription->expires_at);

        return (new MailMessage)
            ->subject('Your subscription expires in ' . $daysLeft . ' days')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your **' . ($this->subscription->plan->title ?? '') . '** subscription expires on **' . $this->subscription->expires_at?->format('M d, Y') . '** (' . $daysLeft . ' days remaining).')
            ->line('Renew now to avoid service interruption and keep your IPTV access uninterrupted.')
            ->action('Renew Subscription', route('pricing.plans'))
            ->line('Thank you for being a valued customer!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your subscription "' . ($this->subscription->plan->title ?? '') . '" expires on ' . $this->subscription->expires_at?->format('M d, Y') . '.',
            'link'    => route('pricing.plans'),
        ];
    }
}
