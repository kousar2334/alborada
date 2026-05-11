<?php

namespace App\Jobs;

use App\Models\UserSubscription;
use App\Services\StripeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSubscriptionRenewalsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(StripeService $stripe): void
    {
        $renewals = UserSubscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->where('auto_renew', true)
            ->where('payment_method', 'stripe')
            ->whereBetween('expires_at', [now(), now()->addDay()])
            ->get();

        foreach ($renewals as $subscription) {
            $success = $stripe->chargeForRenewal($subscription);

            if ($success) {
                dispatch(new ProvisionSubscriptionJob($subscription));
            }
        }
    }
}
