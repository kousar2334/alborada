<?php

namespace App\Services;

use App\Models\PricingPlan;
use App\Models\SubscriptionRenewal;
use App\Models\User;
use Stripe\StripeClient;

/**
 * Stripe gateway wrapper.
 *
 * Keys are stored per environment — `stripe_test_*` for the sandbox and
 * `stripe_live_*` for production — and the active set is chosen by the
 * `stripe_mode` setting. Every read goes through the static resolvers below so
 * that switching the mode in the admin dashboard switches keys, webhook secret
 * and publishable key together.
 */
class StripeService
{
    public const MODE_TEST = 'test';
    public const MODE_LIVE = 'live';

    protected StripeClient $stripe;
    protected string $mode;

    public function __construct()
    {
        $secret = static::secretKey();

        if ($secret === '') {
            throw new \RuntimeException('Stripe secret key is not configured for ' . static::mode() . ' mode.');
        }

        $this->mode   = static::mode();
        $this->stripe = new StripeClient($secret);
    }

    // ── Mode / key resolution ────────────────────────────────────────────────

    /**
     * The active gateway environment: `test` (sandbox) or `live`.
     */
    public static function mode(): string
    {
        return get_setting('stripe_mode', self::MODE_TEST) === self::MODE_LIVE
            ? self::MODE_LIVE
            : self::MODE_TEST;
    }

    public static function isTestMode(): bool
    {
        return static::mode() === self::MODE_TEST;
    }

    public static function isEnabled(): bool
    {
        return (bool) get_setting('stripe_enabled', 0);
    }

    /**
     * Enabled *and* usable — both keys present for the active mode.
     */
    public static function isReady(): bool
    {
        return static::isEnabled()
            && static::secretKey() !== ''
            && static::publishableKey() !== '';
    }

    public static function publishableKey(): string
    {
        return static::resolveKey('public_key', ['pk_test_'], config('services.stripe.key', ''));
    }

    public static function secretKey(): string
    {
        return static::resolveKey('secret_key', ['sk_test_', 'rk_test_'], config('services.stripe.secret', ''));
    }

    public static function webhookSecret(): string
    {
        // Webhook secrets carry no test/live marker, so a legacy value can only be
        // reused when the mode-specific one is missing.
        return static::resolveKey('webhook_secret', null, config('services.stripe.webhook_secret', ''));
    }

    public static function currency(): string
    {
        return strtolower((string) get_setting('stripe_currency', 'usd')) ?: 'usd';
    }

    /**
     * Read a key for the active mode, falling back to the pre-split legacy
     * setting and finally to the .env value.
     *
     * @param  string[]|null  $testPrefixes  Prefixes that identify a sandbox key.
     *                                       When given, a legacy value is only
     *                                       reused if its prefix matches the mode.
     */
    protected static function resolveKey(string $field, ?array $testPrefixes, string $envFallback): string
    {
        $mode  = static::mode();
        $value = trim((string) get_setting('stripe_' . $mode . '_' . $field, ''));

        if ($value !== '') {
            return $value;
        }

        $legacy = trim((string) get_setting('stripe_' . $field, ''));

        if ($legacy !== '' && $testPrefixes !== null) {
            $legacyIsTest = false;
            foreach ($testPrefixes as $prefix) {
                if (str_starts_with($legacy, $prefix)) {
                    $legacyIsTest = true;
                    break;
                }
            }

            // Never hand a live key to sandbox mode (or the reverse).
            if ($legacyIsTest !== ($mode === self::MODE_TEST)) {
                $legacy = '';
            }
        }

        return $legacy !== '' ? $legacy : trim((string) $envFallback);
    }

    // ── Payments ─────────────────────────────────────────────────────────────

    public function createPaymentIntent(PricingPlan $plan, User $user): array
    {
        $customerId = $this->createOrRetrieveCustomer($user);

        $intent = $this->stripe->paymentIntents->create([
            'amount'      => $this->toMinorUnits((float) $plan->effective_price),
            'currency'    => static::currency(),
            'customer'    => $customerId,
            'description' => $plan->title . ' Subscription',
            'metadata'    => [
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'type'    => 'subscription',
            ],
        ]);

        return [
            'client_secret'     => $intent->client_secret,
            'payment_intent_id' => $intent->id,
            'mode'              => $this->mode,
        ];
    }

    /**
     * A customer-initiated (on-session) renewal payment. There is no off-session
     * charging any more — the customer is always present and confirms the card.
     */
    public function createRenewalPaymentIntent(SubscriptionRenewal $renewal, User $user): array
    {
        $customerId = $this->createOrRetrieveCustomer($user);

        $intent = $this->stripe->paymentIntents->create([
            'amount'      => $this->toMinorUnits((float) $renewal->amount),
            'currency'    => static::currency(),
            'customer'    => $customerId,
            'description' => 'Renewal: ' . ($renewal->plan->title ?? 'Subscription'),
            'metadata'    => [
                'type'            => 'renewal',
                'renewal_id'      => $renewal->id,
                'subscription_id' => $renewal->subscription_id,
                'plan_id'         => $renewal->plan_id,
                'user_id'         => $user->id,
            ],
        ]);

        return [
            'client_secret'     => $intent->client_secret,
            'payment_intent_id' => $intent->id,
            'mode'              => $this->mode,
        ];
    }

    public function retrievePaymentIntent(string $paymentIntentId): \Stripe\PaymentIntent
    {
        return $this->stripe->paymentIntents->retrieve($paymentIntentId);
    }

    public function constructWebhookEvent(string $payload, string $sigHeader): \Stripe\Event
    {
        return \Stripe\Webhook::constructEvent($payload, $sigHeader, static::webhookSecret());
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

    /**
     * Verify connectivity and that the stored keys belong to the active mode.
     *
     * @return array{ok: bool, message: string}
     */
    public function testConnection(): array
    {
        // A key mismatch is the most common misconfiguration: live keys pasted
        // into the sandbox panel or the reverse. Catch it before calling out.
        $secretIsTest = str_starts_with(static::secretKey(), 'sk_test_')
            || str_starts_with(static::secretKey(), 'rk_test_');

        if ($secretIsTest !== ($this->mode === self::MODE_TEST)) {
            return [
                'ok'      => false,
                'message' => 'The Secret Key saved for ' . strtoupper($this->mode)
                    . ' mode is a ' . ($secretIsTest ? 'test' : 'live') . ' key. Paste it into the other panel.',
            ];
        }

        try {
            // Balance needs no ID and works with restricted keys, so it is the
            // cheapest way to prove the key is accepted.
            $this->stripe->balance->retrieve();

            return [
                'ok'      => true,
                'message' => 'Connected to Stripe in ' . strtoupper($this->mode) . ' mode.',
            ];
        } catch (\Exception $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public function getPublishableKey(): string
    {
        return static::publishableKey();
    }

    /**
     * Stripe expects the smallest currency unit. Zero-decimal currencies (JPY,
     * KRW, …) must not be multiplied by 100.
     */
    protected function toMinorUnits(float $amount): int
    {
        $zeroDecimal = [
            'bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga',
            'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf',
        ];

        return in_array(static::currency(), $zeroDecimal, true)
            ? (int) round($amount)
            : (int) round($amount * 100);
    }
}
