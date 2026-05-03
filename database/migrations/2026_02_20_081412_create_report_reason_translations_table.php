<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_reason_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reason_id')->constrained('report_reasons')->onDelete('cascade');
            $table->string('lang', 10);
            $table->string('title');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_reason_translations');
    }
};
