<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Provider-agnostic IPTV columns. The old xtream_* columns are kept in
     * place (unused) and their data is backfilled here so existing
     * subscriptions keep working after the provider abstraction lands.
     */
    public function up(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->string('iptv_provider')->nullable()->after('stripe_charge_id');
            $table->string('iptv_user_id')->nullable()->after('iptv_provider');
            $table->string('iptv_username')->nullable()->after('iptv_user_id');
            $table->string('iptv_password')->nullable()->after('iptv_username');
            $table->string('iptv_mac')->nullable()->after('iptv_password');
            $table->text('iptv_m3u_url')->nullable()->after('iptv_mac');
            $table->string('iptv_device_type')->default('m3u')->after('iptv_m3u_url');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('iptv_username')->nullable()->after('xtream_password');
            $table->string('iptv_password')->nullable()->after('iptv_username');
        });

        // Backfill existing Xtream data into the generic columns.
        DB::table('user_subscriptions')
            ->whereNotNull('xtream_username')
            ->update([
                'iptv_provider' => 'xtream',
                'iptv_user_id'  => DB::raw('xtream_line_id'),
                'iptv_username' => DB::raw('xtream_username'),
                'iptv_password' => DB::raw('xtream_password'),
            ]);

        DB::table('users')
            ->whereNotNull('xtream_username')
            ->update([
                'iptv_username' => DB::raw('xtream_username'),
                'iptv_password' => DB::raw('xtream_password'),
            ]);
    }

    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'iptv_provider',
                'iptv_user_id',
                'iptv_username',
                'iptv_password',
                'iptv_mac',
                'iptv_m3u_url',
                'iptv_device_type',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['iptv_username', 'iptv_password']);
        });
    }
};
