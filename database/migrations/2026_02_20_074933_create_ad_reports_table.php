<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained('ads')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reason_id')->nullable()->constrained('report_reasons')->onDelete('set null');
            $table->text('message')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=pending, 1=reviewed, 2=dismissed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_reports');
    }
};
