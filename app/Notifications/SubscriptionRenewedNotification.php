<?php

namespace App\Notifications;

use App\Models\SubscriptionRenewal;
use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionRenewedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public UserSubscription $subscription,
        public SubscriptionRenewal $renewal,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $plan   = $this->subscription->plan->title ?? '';
        $expiry = $this->subscription->expires_at?->format('M d, Y');

        $mail = (new MailMessage)
            ->subject('Your subscription has been renewed')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your **' . $plan . '** subscription has been renewed.')
            ->line('It is now active until **' . $expiry . '**.');

        if ($this->renewal->amount > 0) {
            $mail->line('Amount paid: **' . format_amount($this->renewal->amount) . '** ('
                . str_replace('_', ' ', $this->renewal->payment_method) . ').');
        }

        return $mail
            ->action('View My Subscriptions', route('member.subscriptions'))
            ->line('Thank you for staying with us!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your "' . ($this->subscription->plan->title ?? '')
                . '" subscription was renewed until ' . $this->subscription->expires_at?->format('M d, Y') . '.',
            'link'    => route('member.subscriptions'),
        ];
    }
}
