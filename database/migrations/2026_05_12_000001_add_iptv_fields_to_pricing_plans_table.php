<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            $table->unsignedSmallInteger('max_connections')->default(1)->after('gallery_image_quantity');
            $table->enum('streaming_quality', ['SD', 'HD', 'FHD', '4K'])->default('HD')->after('max_connections');
            $table->unsignedSmallInteger('catchup_days')->default(0)->after('streaming_quality');
            $table->boolean('dvr_enabled')->default(false)->after('catchup_days');
            $table->boolean('is_trial')->default(false)->after('dvr_enabled');
            $table->unsignedSmallInteger('trial_days')->nullable()->after('is_trial');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('trial_days');
        });
    }

    public function down(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            $table->dropColumn([
                'max_connections', 'streaming_quality', 'catchup_days',
                'dvr_enabled', 'is_trial', 'trial_days', 'sort_order',
            ]);
        });
    }
};
