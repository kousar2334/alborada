<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedInteger('duration_days')->default(30);
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('listing_quantity')->default(0);
            $table->unsignedInteger('featured_listing_quantity')->default(0);
            $table->unsignedInteger('gallery_image_quantity')->default(0);
            $table->boolean('membership_badge')->default(false);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_plans');
    }
};
