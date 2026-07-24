<?php

namespace App\Jobs;

use App\Contracts\IptvProvider;
use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Reconcile local subscriptions with the active IPTV provider: re-enable lines
 * that were banned upstream while still valid, and push our expiry when it
 * drifts. Provider-agnostic — resolves whichever provider is currently active.
 */
class SyncXtreamStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(IptvProvider $provider): void
    {
        if (!get_setting('iptv_provisioning_enabled', 0) || get_setting('active_iptv_provider', 'xtream') === 'none') {
            return;
        }

        if (!$provider->isConfigured()) {
            return;
        }

        $subscriptions = UserSubscription::with('plan')
            ->where('status', 'active')
            ->whereNotNull('iptv_username')
            ->get();

        foreach ($subscriptions as $subscription) {
            try {
                $info = $provider->fetchInfo($subscription);

                if (empty($info)) {
                    continue;
                }

                $remoteActive = $info['active'] ?? true;
                $remoteExpiry = $info['expires_at'] ?? null;

                // Provider says the line is disabled but we think it's active — re-enable.
                if (!$remoteActive && $subscription->expires_at?->isFuture()) {
                    $provider->enable($subscription);
                }

                // Provider expiry drifted from our record — push a renewal.
                if ($remoteExpiry && $subscription->expires_at
                    && abs($remoteExpiry->diffInHours($subscription->expires_at)) > 1) {
                    $provider->renew($subscription, $subscription->plan?->iptvMonths() ?? 1);
                }
            } catch (\Exception $e) {
                // Skip this subscription on error; try again next cycle
            }
        }
    }
}
