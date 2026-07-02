<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Rebrand existing installs from "Alborada" to "Moissanite Radiance" and seed
 * the slogan. Fresh installs are covered by the get_setting() fallbacks; this
 * only fixes databases that already stored the old brand.
 */
return new class extends Migration
{
    public function up(): void
    {
        $siteName = DB::table('settings')->where('key', 'site_name')->value('value');
        if ($siteName !== null && str_starts_with($siteName, 'Alborada')) {
            DB::table('settings')->where('key', 'site_name')->update(['value' => 'Moissanite Radiance']);
        }

        $tagline = DB::table('settings')->where('key', 'site_tagline')->value('value');
        if ($tagline === null || $tagline === '') {
            DB::table('settings')->updateOrInsert(
                ['key' => 'site_tagline'],
                ['value' => 'Where every Stream sparkles']
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
