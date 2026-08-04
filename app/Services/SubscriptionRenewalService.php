<?php

namespace App\Services;

use App\Models\SubscriptionRenewal;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionRenewedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Every renewal path — admin manual renew, customer card payment, customer bank
 * transfer approved by an admin — funnels through here so the subscription
 * period, the streaming panel and the paperwork always move together.
 */
class SubscriptionRenewalService
{
    public function __construct(
        protected IptvProvisioningService $provisioning,
        protected InvoiceService $invoices,
    ) {}

    /**
     * A subscription can be renewed while it is running or after it lapsed, but
     * not while a payment is still pending or after it was rejected.
     */
    public function canRenew(UserSubscription $subscription): bool
    {
        return in_array($subscription->status, ['active', 'expired'], true)
            && $subscription->plan !== null;
    }

    /**
     * Record a renewal intent. Nothing is extended until it is marked paid via
     * {@see markPaid()} — this row is what a pending card payment or an
     * unapproved bank transfer hangs off.
     */
    public function createPending(UserSubscription $subscription, array $attributes = []): SubscriptionRenewal
    {
        $plan = $subscription->plan;

        // Abandoned card checkouts would otherwise pile up as "pending" forever.
        if (($attributes['payment_method'] ?? null) === 'stripe') {
            SubscriptionRenewal::where('subscription_id', $subscription->id)
                ->where('payment_method', 'stripe')
                ->where('status', 'pending')
                ->update([
                    'status'     => 'failed',
                    'admin_note' => 'Superseded — the customer started a new payment.',
                ]);
        }

        return SubscriptionRenewal::create(array_merge([
            'subscription_id'     => $subscription->id,
            'user_id'             => $subscription->user_id,
            'plan_id'             => $subscription->plan_id,
            'transaction_id'      => $this->transactionId($attributes['payment_method'] ?? 'manual'),
            'amount'              => $plan?->effective_price ?? 0,
            'days'                => (int) ($plan?->duration_days ?? 30),
            'months'              => $plan?->iptvMonths() ?? 1,
            'payment_method'      => 'manual',
            'status'              => 'pending',
            'previous_expires_at' => $subscription->expires_at,
        ], $attributes));
    }

    /**
     * Apply a paid renewal: push the expiry out by one plan period, extend the
     * streaming account, issue the receipt and notify the customer.
     *
     * Idempotent — a renewal that is already `paid` is a no-op, so the Stripe
     * webhook and the browser return path can both call it for one payment.
     */
    public function markPaid(SubscriptionRenewal $renewal, array $attributes = []): bool
    {
        // Claim the renewal with a conditional update so a webhook and a browser
        // return racing on the same payment can never both extend the period.
        $claimed = SubscriptionRenewal::whereKey($renewal->getKey())
            ->where('status', '!=', 'paid')
            ->update(['status' => 'paid', 'paid_at' => now()]);

        if ($claimed === 0) {
            return true;
        }

        $renewal->refresh();

        $subscription = $renewal->subscription()->with(['plan', 'user'])->first();

        if (!$subscription) {
            $renewal->update(['status' => 'failed', 'admin_note' => 'Subscription no longer exists.']);
            return false;
        }

        $plan = $subscription->plan;
        $days = (int) ($renewal->days ?: $plan?->duration_days ?: 30);

        // Renewing early must not burn the remaining time: extend from the current
        // expiry when it is still in the future, otherwise from now.
        $from       = $subscription->expires_at && $subscription->expires_at->isFuture()
            ? $subscription->expires_at->copy()
            : now();
        $newExpiry  = $from->addDays($days);

        DB::transaction(function () use ($renewal, $subscription, $newExpiry, $attributes) {
            $renewal->update(array_merge([
                'status'              => 'paid',
                'paid_at'             => now(),
                'previous_expires_at' => $renewal->previous_expires_at ?? $subscription->expires_at,
                'new_expires_at'      => $newExpiry,
            ], $attributes));

            $subscription->update([
                'status'                => 'active',
                'starts_at'             => $subscription->starts_at ?? now(),
                'expires_at'            => $newExpiry,
                'renewal_reminder_sent' => false,
                'expiry_alert_sent'     => false,
            ]);
        });

        $subscription->refresh();

        // Extend (or create, if it was never provisioned) the streaming account.
        if (get_setting('iptv_provisioning_enabled', 0)) {
            try {
                $this->provisioning->renew($subscription);
            } catch (\Throwable $e) {
                \Log::error('Renewal provisioning failed for subscription ' . $subscription->id . ': ' . $e->getMessage());
            }
        }

        $this->generateInvoice($renewal, $subscription);
        $this->notify($renewal, $subscription);

        return true;
    }

    public function markFailed(SubscriptionRenewal $renewal, ?string $note = null): void
    {
        if ($renewal->isPaid()) {
            return;
        }

        $renewal->update(array_filter([
            'status'     => 'failed',
            'admin_note' => $note,
        ]));
    }

    public function reject(SubscriptionRenewal $renewal, ?string $note = null, ?int $adminId = null): void
    {
        if ($renewal->isPaid()) {
            return;
        }

        $renewal->update([
            'status'       => 'rejected',
            'admin_note'   => $note,
            'processed_by' => $adminId,
        ]);
    }

    public function transactionId(string $method): string
    {
        $prefix = match ($method) {
            'stripe'        => 'RNW-STRIPE',
            'bank_transfer' => 'RNW-BANK',
            default         => 'RNW-MANUAL',
        };

        return $prefix . '-' . strtoupper(Str::random(10));
    }

    /**
     * Paid receipt for the renewal. Kept off the subscription's own `invoice_id`
     * so the original purchase receipt is not overwritten. A failure here must
     * not undo an applied renewal.
     */
    private function generateInvoice(SubscriptionRenewal $renewal, UserSubscription $subscription): void
    {
        if ($renewal->invoice_id || $renewal->amount <= 0) {
            return;
        }

        try {
            $invoice = $this->invoices->createForSubscription($subscription, [
                'amount'       => $renewal->amount,
                'total_amount' => $renewal->amount,
                'notes'        => 'Renewal ' . $renewal->transaction_id
                    . ' — extended to ' . $renewal->new_expires_at?->format('M d, Y'),
            ], attachToSubscription: false);

            $renewal->update(['invoice_id' => $invoice->id]);

            $this->invoices->sendByEmail($invoice);
        } catch (\Exception $e) {
            \Log::error('Renewal invoice failed for renewal ' . $renewal->id . ': ' . $e->getMessage());
        }
    }

    private function notify(SubscriptionRenewal $renewal, UserSubscription $subscription): void
    {
        try {
            $subscription->user?->notify(new SubscriptionRenewedNotification($subscription, $renewal));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
