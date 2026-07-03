<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 50)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'social_provider')) {
                $table->string('social_provider', 50)->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'social_id')) {
                $table->string('social_id')->nullable()->after('social_provider');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'social_provider', 'social_id']);
        });
    }
};
