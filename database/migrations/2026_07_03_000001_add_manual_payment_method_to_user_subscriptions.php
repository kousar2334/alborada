<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN payment_method ENUM('trial','bank_transfer','sslcommerz','stripe','manual') DEFAULT 'trial'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE user_subscriptions MODIFY COLUMN payment_method ENUM('trial','bank_transfer','sslcommerz','stripe') DEFAULT 'trial'");
    }
};
