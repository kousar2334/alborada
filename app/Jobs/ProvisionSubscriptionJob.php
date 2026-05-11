<?php

namespace App\Jobs;

use App\Models\UserSubscription;
use App\Services\IptvProvisioningService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProvisionSubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public UserSubscription $subscription) {}

    public function handle(IptvProvisioningService $service): void
    {
        $service->provision($this->subscription);
    }
}
