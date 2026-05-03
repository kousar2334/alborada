<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads_custom_field_option_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_id')->constrained('ads_custom_field_options')->cascadeOnDelete();
            $table->string('lang', 10);
            $table->string('value', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads_custom_field_option_translations');
    }
};
