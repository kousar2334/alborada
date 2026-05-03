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
        Schema::create('blog_has_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs', 'id')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('blog_categories', 'id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_has_categories');
    }
};
