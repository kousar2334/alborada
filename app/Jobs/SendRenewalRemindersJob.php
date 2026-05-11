<?php

namespace App\Jobs;

use App\Models\UserSubscription;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRenewalRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $subscriptions = UserSubscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->where('renewal_reminder_sent', false)
            ->whereBetween('expires_at', [now()->addDays(6)->startOfDay(), now()->addDays(7)->endOfDay()])
            ->get();

        foreach ($subscriptions as $subscription) {
            $subscription->user->notify(new SubscriptionExpiringNotification($subscription));
            $subscription->update(['renewal_reminder_sent' => true]);
        }
    }
}
