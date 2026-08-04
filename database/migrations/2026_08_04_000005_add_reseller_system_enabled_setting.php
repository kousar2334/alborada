<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

/**
 * Master switch for the reseller module. Seeded as enabled so installs that are
 * already selling through resellers are unaffected by the new setting.
 */
return new class extends Migration
{
    public function up(): void
    {
        Setting::firstOrCreate(['key' => 'reseller_system_enabled'], ['value' => 1]);

        cache()->forget('settings');
    }

    public function down(): void
    {
        Setting::where('key', 'reseller_system_enabled')->delete();

        cache()->forget('settings');
    }
};
