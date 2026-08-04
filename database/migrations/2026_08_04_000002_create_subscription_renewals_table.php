<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per renewal attempt on a subscription. A subscription row only holds
 * the *current* period, so renewals need their own history: who renewed, how
 * they paid, which Stripe mode the payment ran in, and the period it bought.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_renewals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('plan_id')->nullable();

            $table->string('transaction_id')->nullable()->unique();
            $table->decimal('amount', 12, 2)->default(0);
            $table->unsignedSmallInteger('days')->default(30);
            $table->unsignedSmallInteger('months')->default(1);

            // stripe | bank_transfer | manual
            $table->string('payment_method', 32)->default('manual');
            // pending | paid | failed | rejected
            $table->string('status', 16)->default('pending');

            $table->string('stripe_payment_intent_id')->nullable()->index();
            $table->string('stripe_charge_id')->nullable();
            $table->string('stripe_mode', 8)->nullable();

            $table->string('bank_transaction_number')->nullable();
            $table->string('bank_slip')->nullable();

            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->text('admin_note')->nullable();

            $table->timestamp('previous_expires_at')->nullable();
            $table->timestamp('new_expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['subscription_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_renewals');
    }
};
