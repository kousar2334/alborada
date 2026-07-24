<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Provider mapping fields for a plan. Used by the 8K provider (bouquet id,
     * months, device type, country). Xtream ignores them and uses duration_days.
     */
    public function up(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            $table->string('iptv_package_id')->nullable()->after('max_connections');
            $table->unsignedTinyInteger('iptv_sub_months')->default(1)->after('iptv_package_id');
            $table->string('iptv_device_type')->default('m3u')->after('iptv_sub_months');
            $table->string('iptv_country')->default('ALL')->after('iptv_device_type');
        });
    }

    public function down(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            $table->dropColumn([
                'iptv_package_id',
                'iptv_sub_months',
                'iptv_device_type',
                'iptv_country',
            ]);
        });
    }
};
