<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Auto-renewal (off-session Stripe charges on a schedule) is gone. Renewals are
 * now always explicit: an admin renews from the dashboard, or the customer pays
 * for a renewal themselves.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('user_subscriptions', 'auto_renew')) {
            Schema::table('user_subscriptions', function (Blueprint $table) {
                $table->dropColumn('auto_renew');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('user_subscriptions', 'auto_renew')) {
            Schema::table('user_subscriptions', function (Blueprint $table) {
                $table->boolean('auto_renew')->default(false)->after('invoice_id');
            });
        }
    }
};
