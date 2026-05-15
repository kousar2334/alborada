<?php

namespace App\Services;

use App\Models\PricingPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;

class StripeService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $secret = get_setting('stripe_secret_key') ?: config('services.stripe.secret');

        if (empty($secret)) {
            throw new \RuntimeException('Stripe secret key is not configured.');
        }

        $this->stripe = new StripeClient($secret);
    }

    public function createPaymentIntent(PricingPlan $plan, User $user): array
    {
        $customerId = $this->createOrRetrieveCustomer($user);

        $intent = $this->stripe->paymentIntents->create([
            'amount'      => (int) ($plan->price * 100), // cents
            'currency'    => strtolower(get_setting('stripe_currency', 'usd')),
            'customer'    => $customerId,
            'description' => $plan->title . ' Subscription',
            'metadata'    => [
                'plan_id' => $plan->id,
                'user_id' => $user->id,
            ],
        ]);

        return [
            'client_secret'     => $intent->client_secret,
            'payment_intent_id' => $intent->id,
        ];
    }

    public function retrievePaymentIntent(string $paymentIntentId): \Stripe\PaymentIntent
    {
        return $this->stripe->paymentIntents->retrieve($paymentIntentId);
    }

    public function constructWebhookEvent(string $payload, string $sigHeader): \Stripe\Event
    {
        $webhookSecret = get_setting('stripe_webhook_secret', config('services.stripe.webhook_secret'));

        return \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
    }

    public function createOrRetrieveCustomer(User $user): string
    {
        if ($user->stripe_customer_id) {
            return $user->stripe_customer_id;
        }

        $customer = $this->stripe->customers->create([
            'email' => $user->email,
            'name'  => $user->name,
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer->id;
    }

    public function chargeForRenewal(UserSubscription $subscription): bool
    {
        if (!$subscription->stripe_payment_intent_id) {
            return false;
        }

        try {
            $user = $subscription->user;
            $plan = $subscription->plan;

            $customerId = $this->createOrRetrieveCustomer($user);

            $customer = $this->stripe->customers->retrieve($customerId, [
                'expand' => ['default_source'],
            ]);

            if (!$customer->default_source && empty($customer->invoice_settings->default_payment_method)) {
                return false;
            }

            $intent = $this->stripe->paymentIntents->create([
                'amount'               => (int) ($plan->price * 100),
                'currency'             => strtolower(get_setting('stripe_currency', 'usd')),
                'customer'             => $customerId,
                'confirm'              => true,
                'payment_method'       => $customer->invoice_settings->default_payment_method,
                'description'          => 'Auto-renewal: ' . $plan->title,
                'off_session'          => true,
                'metadata'             => [
                    'plan_id'         => $plan->id,
                    'user_id'         => $user->id,
                    'subscription_id' => $subscription->id,
                    'type'            => 'renewal',
                ],
            ]);

            if ($intent->status === 'succeeded') {
                $subscription->update([
                    'stripe_payment_intent_id' => $intent->id,
                    'stripe_charge_id'          => $intent->latest_charge,
                    'status'                    => 'active',
                    'starts_at'                 => now(),
                    'expires_at'                => now()->addDays($plan->duration_days),
                    'renewal_reminder_sent'     => false,
                    'expiry_alert_sent'         => false,
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getPublishableKey(): string
    {
        return get_setting('stripe_public_key', config('services.stripe.key', ''));
    }
}
