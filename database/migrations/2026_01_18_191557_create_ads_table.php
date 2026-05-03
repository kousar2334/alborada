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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 250)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('category_id')->nullable()->constrained('ads_categories')->onDelete('set null')->onUpdate('cascade');
            $table->string('title', 250);
            $table->double('price')->default(0);
            $table->double('cost')->default(0);
            $table->integer('is_negotiable')->default(2);
            $table->text('description')->nullable();
            $table->foreignId('condition_id')->nullable()->constrained('ads_conditions')->onDelete('set null')->onUpdate('cascade');
            $table->string('thumbnail_image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('contact_email', 100)->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->integer('contact_is_hide')->default(1);
            $table->integer('is_featured')->default(2);
            $table->integer('payment_method')->nullable();
            $table->json('custom_field')->nullable();
            $table->double('view_counter')->default(0);
            $table->integer('is_sold')->default(0);
            $table->integer('sellable')->default(0);
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('state_id')->nullable()->constrained('states')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null')->onUpdate('cascade');
            $table->text('address')->nullable();
            $table->integer('payment_status')->default(2);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
