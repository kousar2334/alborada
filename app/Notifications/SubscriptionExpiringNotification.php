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
            ->line('Renew now to avoid service interruption and keep your IPTV access uninterrupted. Renewing early does not waste time — the new period is added on top of your current expiry date.')
            ->action('Renew Subscription', $this->renewUrl())
            ->line('Thank you for being a valued customer!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your subscription "' . ($this->subscription->plan->title ?? '') . '" expires on ' . $this->subscription->expires_at?->format('M d, Y') . '.',
            'link'    => $this->renewUrl(),
        ];
    }

    /**
     * Renewal checkout for this subscription — falls back to the plan list when
     * the subscription has no plan to renew onto.
     */
    private function renewUrl(): string
    {
        return $this->subscription->plan_id
            ? route('subscription.renew', $this->subscription->id)
            : route('pricing.plans');
    }
}
