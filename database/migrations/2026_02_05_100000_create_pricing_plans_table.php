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
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title', 250);
            $table->integer('duration_days')->default(30);
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('listing_quantity')->default(0);
            $table->integer('featured_listing_quantity')->default(0);
            $table->integer('gallery_image_quantity')->default(0);
            $table->integer('membership_badge')->default(0)->comment('0=disabled, 1=enabled');
            $table->integer('online_shop')->default(0)->comment('0=disabled, 1=enabled');
            $table->integer('status')->default(1)->comment('1=active, 0=inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_plans');
    }
};
