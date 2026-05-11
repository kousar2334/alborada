<?php

namespace App\Jobs;

use App\Models\UserSubscription;
use App\Services\XtreamCodesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncXtreamStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(XtreamCodesService $xtream): void
    {
        if (!get_setting('iptv_provisioning_enabled', 0)) {
            return;
        }

        $subscriptions = UserSubscription::with('plan')
            ->where('status', 'active')
            ->whereNotNull('xtream_username')
            ->get();

        foreach ($subscriptions as $subscription) {
            try {
                $info = $xtream->getLineInfo($subscription->xtream_username);

                if (empty($info)) {
                    continue;
                }

                $xtreamActive = ($info['user_info']['active'] ?? 1) == 1;
                $xtreamExpiry = isset($info['user_info']['exp_date'])
                    ? \Carbon\Carbon::createFromTimestamp($info['user_info']['exp_date'])
                    : null;

                // If Xtream says the line is banned but we think it's active — re-activate
                if (!$xtreamActive && $subscription->expires_at?->isFuture()) {
                    $xtream->unbanLine($subscription->xtream_username);
                }

                // If Xtream expiry drifted from our record — update Xtream
                if ($xtreamExpiry && $subscription->expires_at && abs($xtreamExpiry->diffInHours($subscription->expires_at)) > 1) {
                    $xtream->updateLine($subscription->xtream_username, [
                        'exp_date' => $subscription->expires_at->timestamp,
                    ]);
                }
            } catch (\Exception $e) {
                // Skip this subscription on error; try again next cycle
            }
        }
    }
}
