<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ads_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title', 250)->nullable();
            $table->string('permalink', 250);
            $table->foreignId('parent')->nullable()->constrained('ads_categories')->onDelete('set null')->onUpdate('cascade');
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_categories');
    }
};
