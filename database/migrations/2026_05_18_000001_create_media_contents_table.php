<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('trailer_url')->nullable();
            $table->enum('type', ['movie', 'tv_show'])->default('movie');
            $table->string('genre')->nullable();
            $table->unsignedSmallInteger('release_year')->nullable();
            $table->unsignedSmallInteger('seasons')->nullable();
            $table->unsignedSmallInteger('episodes')->nullable();
            $table->string('cast')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->string('badge_text', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('featured_on_home')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_contents');
    }
};
