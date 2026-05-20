<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('home_page_sections')) {
            return;
        }

        DB::table('home_page_sections')->whereIn('key', ['upcoming_events', 'featured_content'])->delete();

        $inserts = [
            ['key' => 'movies',       'title' => 'Movies',       'sort_order' => 25,  'is_active' => 1],
            ['key' => 'series',       'title' => 'Series',       'sort_order' => 26,  'is_active' => 1],
            ['key' => 'sport_events', 'title' => 'Sport Events', 'sort_order' => 55,  'is_active' => 1],
        ];

        foreach ($inserts as $row) {
            DB::table('home_page_sections')->insertOrIgnore(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('home_page_sections')) {
            return;
        }

        DB::table('home_page_sections')->whereIn('key', ['movies', 'series', 'sport_events'])->delete();

        $inserts = [
            ['key' => 'featured_content', 'title' => 'Featured Content', 'sort_order' => 25, 'is_active' => 1],
            ['key' => 'upcoming_events',  'title' => 'Upcoming Events',  'sort_order' => 55, 'is_active' => 1],
        ];

        foreach ($inserts as $row) {
            DB::table('home_page_sections')->insertOrIgnore(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
};
