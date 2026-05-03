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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->nullable()->constrained('menus', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('parent')->nullable()->constrained('menu_items', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('item_type');
            $table->nullableMorphs('linkable');
            $table->string('title')->nullable();
            $table->string('link');
            $table->integer('position');
            $table->integer('target')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
