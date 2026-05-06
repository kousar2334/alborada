<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('phone');
            $table->foreignId('reseller_id')->nullable()->constrained('users')->nullOnDelete()->after('company_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['reseller_id']);
            $table->dropColumn(['company_name', 'reseller_id']);
        });
    }
};
