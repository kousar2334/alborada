<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN payment_method ENUM('trial','bank_transfer','sslcommerz','stripe') DEFAULT 'trial'");
        DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN status ENUM('pending','active','failed','rejected','cancelled','expired') DEFAULT 'pending'");

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->string('stripe_payment_intent_id')->nullable()->after('ssl_val_id');
            $table->string('stripe_charge_id')->nullable()->after('stripe_payment_intent_id');
            $table->string('xtream_username')->nullable()->after('stripe_charge_id');
            $table->string('xtream_password')->nullable()->after('xtream_username');
            $table->string('xtream_line_id')->nullable()->after('xtream_password');
            $table->unsignedBigInteger('invoice_id')->nullable()->after('xtream_line_id');
            $table->boolean('auto_renew')->default(false)->after('invoice_id');
            $table->boolean('renewal_reminder_sent')->default(false)->after('auto_renew');
            $table->boolean('expiry_alert_sent')->default(false)->after('renewal_reminder_sent');
        });
    }

    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_payment_intent_id', 'stripe_charge_id',
                'xtream_username', 'xtream_password', 'xtream_line_id',
                'invoice_id', 'auto_renew', 'renewal_reminder_sent', 'expiry_alert_sent',
            ]);
        });

        DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN payment_method ENUM('trial','bank_transfer','sslcommerz') DEFAULT 'trial'");
        DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN status ENUM('pending','active','failed','rejected','cancelled') DEFAULT 'pending'");
    }
};
