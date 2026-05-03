<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_plan_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('pricing_plans')->cascadeOnDelete();
            $table->string('lang');
            $table->string('title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_plan_translations');
    }
};
