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
        Schema::create('ads_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('title', 250);
            $table->foreignId('category_id')->nullable()->constrained('ads_categories')->onDelete('set null')->onUpdate('cascade')->nullable();
            $table->integer('type');
            $table->integer('is_required')->default(2);
            $table->integer('is_filterable')->default(2);
            $table->string('default_value', 250)->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_custom_fields');
    }
};
