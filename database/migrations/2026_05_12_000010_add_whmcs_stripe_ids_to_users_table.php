<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('whmcs_client_id')->nullable()->after('xtream_password');
            $table->string('stripe_customer_id')->nullable()->after('whmcs_client_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['whmcs_client_id', 'stripe_customer_id']);
        });
    }
};
