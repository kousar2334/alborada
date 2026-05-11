<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('credits', 10, 2)->default(0.00)->after('reseller_id');
            $table->decimal('markup_percentage', 5, 2)->default(0.00)->after('credits');
            $table->string('xtream_username')->nullable()->unique()->after('markup_percentage');
            $table->string('xtream_password')->nullable()->after('xtream_username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['credits', 'markup_percentage', 'xtream_username', 'xtream_password']);
        });
    }
};
