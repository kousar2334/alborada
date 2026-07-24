<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Streaming packages (bouquets) synced from the active provider so pricing
     * plans can be mapped to a provider package id.
     */
    public function up(): void
    {
        Schema::create('iptv_packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_id')->unique();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iptv_packages');
    }
};
