<?php

namespace App\Jobs;

use App\Models\UserSubscription;
use App\Services\IptvProvisioningService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendExpiryAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(IptvProvisioningService $service): void
    {
        $expired = UserSubscription::with(['user', 'plan'])
            ->whereIn('status', ['active'])
            ->where('expiry_alert_sent', false)
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expired as $subscription) {
            $service->expire($subscription);
        }
    }
}
