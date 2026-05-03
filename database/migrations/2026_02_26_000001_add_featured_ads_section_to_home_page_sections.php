<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('home_page_sections')->insertOrIgnore([
            [
                'key'        => 'featured_ads',
                'title'      => 'Featured Ads',
                'sort_order' => 45,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('home_page_sections')->where('key', 'featured_ads')->delete();
    }
};
