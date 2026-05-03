<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('pricing_plans')->onDelete('cascade');
            $table->string('transaction_id')->unique()->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('payment_method')->default('trial')->comment('trial, sslcommerz');
            $table->string('status')->default('pending')->comment('pending, active, expired, failed, cancelled');
            $table->string('ssl_session_key')->nullable();
            $table->string('ssl_val_id')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
