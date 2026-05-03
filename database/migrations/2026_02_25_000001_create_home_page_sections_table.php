<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('home_page_sections')->insert([
            ['key' => 'banner',         'title' => 'Hero Banner',        'sort_order' => 10,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ad_slot',        'title' => 'Advertisement Slot', 'sort_order' => 20,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'categories',     'title' => 'Browse Categories',  'sort_order' => 30,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'top_listings',   'title' => 'Top Listings',       'sort_order' => 40,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'promo',          'title' => 'Promo Section',      'sort_order' => 50,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pricing_plans',  'title' => 'Membership Plans',   'sort_order' => 60,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'recent_listings', 'title' => 'Recent Listings',    'sort_order' => 70,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('home_page_sections');
    }
};
