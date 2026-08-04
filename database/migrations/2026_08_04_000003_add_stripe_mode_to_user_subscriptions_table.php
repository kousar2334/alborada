<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Records whether a Stripe payment was taken in sandbox (test) or live mode, so
 * test-mode orders are distinguishable from real revenue in reports.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('user_subscriptions', 'stripe_mode')) {
            Schema::table('user_subscriptions', function (Blueprint $table) {
                $table->string('stripe_mode', 8)->nullable()->after('stripe_charge_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('user_subscriptions', 'stripe_mode')) {
            Schema::table('user_subscriptions', function (Blueprint $table) {
                $table->dropColumn('stripe_mode');
            });
        }
    }
};
