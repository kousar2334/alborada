<?php

use App\Jobs\ProcessSubscriptionRenewalsJob;
use App\Jobs\SendExpiryAlertsJob;
use App\Jobs\SendRenewalRemindersJob;
use App\Jobs\SyncXtreamStatusJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new ProcessSubscriptionRenewalsJob)->daily();
Schedule::job(new SendRenewalRemindersJob)->dailyAt('08:00');
Schedule::job(new SendExpiryAlertsJob)->dailyAt('09:00');
Schedule::job(new SyncXtreamStatusJob)->everyFourHours();
