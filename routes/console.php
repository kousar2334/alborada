<?php

use App\Jobs\SendExpiryAlertsJob;
use App\Jobs\SendRenewalRemindersJob;
use App\Jobs\SyncXtreamStatusJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Renewals are never charged automatically — customers renew from their
// dashboard, or an admin renews on their behalf. Only reminders are scheduled.
Schedule::job(new SendRenewalRemindersJob)->dailyAt('08:00');
Schedule::job(new SendExpiryAlertsJob)->dailyAt('09:00');
Schedule::job(new SyncXtreamStatusJob)->everyFourHours();
