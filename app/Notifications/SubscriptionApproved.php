<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionApproved extends Notification
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
            ->subject('Subscription Approved - ' . get_setting('site_name'))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your bank transfer payment has been verified and your subscription is now active.')
            ->line('**Plan:** ' . ($this->subscription->plan->title ?? 'N/A'))
            ->line('**Amount:** $' . number_format($this->subscription->amount, 2))
            ->line('**Expires:** ' . $this->subscription->expires_at?->format('M d, Y'))
            ->action('View My Subscription', route('member.subscriptions'))
            ->line('Thank you for subscribing!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your subscription for "' . ($this->subscription->plan->title ?? 'N/A') . '" has been approved and is now active.',
            'link'    => route('member.subscriptions'),
        ];
    }
}
