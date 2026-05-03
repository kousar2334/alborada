<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads_custom_field_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('ads_custom_fields')->cascadeOnDelete();
            $table->string('lang', 10);
            $table->string('title', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads_custom_field_translations');
    }
};
