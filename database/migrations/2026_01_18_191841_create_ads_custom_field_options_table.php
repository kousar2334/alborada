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
        Schema::create('ads_custom_field_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->nullable()->constrained('ads_custom_fields')->onDelete('cascade')->onUpdate('cascade');
            $table->string('value', 250)->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_custom_field_options');
    }
};
