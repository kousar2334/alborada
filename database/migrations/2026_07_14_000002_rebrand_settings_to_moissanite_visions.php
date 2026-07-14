<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The client confirmed the final brand name: "Moissanite Visions".
 * Update installs that stored the earlier names ("Alborada" or
 * "Moissanite Radiance"). Fresh installs are covered by the
 * get_setting() fallbacks in the views.
 */
return new class extends Migration
{
    public function up(): void
    {
        $siteName = DB::table('settings')->where('key', 'site_name')->value('value');
        if (
            $siteName === null ||
            $siteName === '' ||
            str_starts_with($siteName, 'Alborada') ||
            str_starts_with($siteName, 'Moissanite Radiance')
        ) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'site_name'],
                ['value' => 'Moissanite Visions']
            );
        }

        // Invalidate the cached settings blob so the change is visible immediately.
        cache()->forget('settings');
    }

    public function down(): void
    {
        // No-op: rebrand is not reversed automatically.
    }
};
