<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('whmcs_order_id')->nullable()->after('invoice_id');
            $table->unsignedBigInteger('whmcs_service_id')->nullable()->after('whmcs_order_id');
            $table->unsignedBigInteger('whmcs_invoice_id')->nullable()->after('whmcs_service_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['whmcs_order_id', 'whmcs_service_id', 'whmcs_invoice_id']);
        });
    }
};
