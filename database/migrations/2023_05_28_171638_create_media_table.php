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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->string('title');
            $table->string('file_name');
            $table->string('alt')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size');
            $table->string('path');
            $table->string('disk')->nullable();
            $table->json('variant')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users', 'id')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
