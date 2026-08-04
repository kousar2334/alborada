<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

/**
 * Stripe keys used to live in a single set of settings. Split them into separate
 * sandbox (test) and live sets plus a `stripe_mode` switch. Existing keys are
 * moved into whichever set their prefix identifies (`sk_test_`/`pk_test_` →
 * sandbox), and the mode is pointed at that set so live sites keep working.
 */
return new class extends Migration
{
    public function up(): void
    {
        $legacyPublic  = (string) (Setting::where('key', 'stripe_public_key')->value('value') ?? '');
        $legacySecret  = (string) (Setting::where('key', 'stripe_secret_key')->value('value') ?? '');
        $legacyWebhook = (string) (Setting::where('key', 'stripe_webhook_secret')->value('value') ?? '');

        // A test secret key is the only reliable signal; fall back to the
        // publishable key, then default to sandbox for fresh installs.
        $mode = 'test';
        if ($legacySecret !== '') {
            $mode = str_starts_with($legacySecret, 'sk_test_') || str_starts_with($legacySecret, 'rk_test_')
                ? 'test' : 'live';
        } elseif ($legacyPublic !== '') {
            $mode = str_starts_with($legacyPublic, 'pk_test_') ? 'test' : 'live';
        }

        $settings = [
            'stripe_mode'                    => $mode,
            'stripe_' . $mode . '_public_key'     => $legacyPublic,
            'stripe_' . $mode . '_secret_key'     => $legacySecret,
            'stripe_' . $mode . '_webhook_secret' => $legacyWebhook,
        ];

        // Make sure the opposite mode's keys exist (empty) so the admin form has
        // rows to write into.
        $other = $mode === 'test' ? 'live' : 'test';
        foreach (['public_key', 'secret_key', 'webhook_secret'] as $field) {
            $settings['stripe_' . $other . '_' . $field] = '';
        }

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        cache()->forget('settings');
    }

    public function down(): void
    {
        $mode = (string) (Setting::where('key', 'stripe_mode')->value('value') ?? 'live');

        foreach (['public_key', 'secret_key', 'webhook_secret'] as $field) {
            $value = Setting::where('key', 'stripe_' . $mode . '_' . $field)->value('value');
            Setting::updateOrCreate(['key' => 'stripe_' . $field], ['value' => $value ?? '']);
        }

        Setting::whereIn('key', [
            'stripe_mode',
            'stripe_test_public_key', 'stripe_test_secret_key', 'stripe_test_webhook_secret',
            'stripe_live_public_key', 'stripe_live_secret_key', 'stripe_live_webhook_secret',
        ])->delete();

        cache()->forget('settings');
    }
};
