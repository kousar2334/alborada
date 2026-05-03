<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $now = Carbon::now();

        DB::table('languages')->updateOrInsert(
            ['code' => 'en'], // unique code
            [
                'title' => 'English',
                'native_title' => 'English',
                'icon' => '🇺🇸',
                'is_rtl' => 0,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
