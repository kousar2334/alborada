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
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('pricing_plans')->cascadeOnDelete();
            $table->string('transaction_id')->nullable()->unique();
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('payment_method', ['trial', 'bank_transfer', 'sslcommerz'])->default('trial');
            $table->enum('status', ['pending', 'active', 'failed', 'rejected', 'cancelled'])->default('pending');
            $table->string('bank_transaction_number')->nullable();
            $table->string('bank_slip')->nullable();
            $table->string('ssl_session_key')->nullable();
            $table->string('ssl_val_id')->nullable();
            $table->text('admin_note')->nullable();
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
