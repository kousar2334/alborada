<?php

namespace App\Providers;

use App\Contracts\IptvProvider;
use App\Services\EightKApiService;
use App\Services\XtreamCodesService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Resolve the active IPTV streaming provider from settings. Only one
        // provider is active at a time (`active_iptv_provider`); WHMCS billing
        // sync is handled separately.
        $this->app->bind(IptvProvider::class, function () {
            return match (get_setting('active_iptv_provider', 'xtream')) {
                '8k'   => new EightKApiService(),
                default => new XtreamCodesService(),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url(route('member.reset.password', ['token' => $token, 'email' => $user->email]));
        });
    }
}
