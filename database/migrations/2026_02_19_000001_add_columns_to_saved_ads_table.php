<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saved_ads', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id');
            $table->unsignedBigInteger('ad_id')->after('user_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');

            $table->unique(['user_id', 'ad_id']);
        });
    }

    public function down(): void
    {
        Schema::table('saved_ads', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['ad_id']);
            $table->dropUnique(['user_id', 'ad_id']);
            $table->dropColumn(['user_id', 'ad_id']);
        });
    }
};
