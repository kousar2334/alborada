<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads_condition_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condition_id')->constrained('ads_conditions')->cascadeOnDelete();
            $table->string('lang', 10);
            $table->string('title', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads_condition_translations');
    }
};
