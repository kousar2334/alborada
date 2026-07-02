<?php

namespace App\Services;

use App\Mail\WelcomeWithCredentialsMail;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class IptvProvisioningService
{
    public function __construct(
        protected XtreamCodesService $xtream,
        protected WhmcsService $whmcs,
        protected InvoiceService $invoices,
    ) {}

    public function provision(UserSubscription $subscription): bool
    {
        // Idempotency guard: if this subscription already has a provisioned line,
        // don't create a duplicate on the panel. The Stripe webhook and the
        // browser-return path can both fire for the same payment.
        if ($subscription->xtream_line_id) {
            return true;
        }

        $user = $subscription->user;
        $plan = $subscription->plan;

        try {
            // Generate Xtream credentials if not already set
            $username = $subscription->xtream_username ?? ('alb_' . $user->id . '_' . strtolower(Str::random(4)));
            $password = $subscription->xtream_password ?? Str::random(12);

            $expDate = $subscription->expires_at
                ? $subscription->expires_at->timestamp
                : now()->addDays($plan->duration_days)->timestamp;

            $lineData = $this->xtream->createLine([
                'username'               => $username,
                'password'               => $password,
                'max_connections'        => $plan->max_connections ?? 1,
                'allowed_output_formats' => ['ts', 'm3u8'],
                'is_trial'               => $plan->is_trial ? '1' : '0',
                'exp_date'               => $expDate,
            ]);

            $lineId = $lineData['user_info']['id'] ?? $lineData['id'] ?? null;

            // Store credentials on subscription and user
            $subscription->update([
                'xtream_username' => $username,
                'xtream_password' => $password,
                'xtream_line_id'  => $lineId,
            ]);

            $user->update([
                'xtream_username' => $username,
                'xtream_password' => $password,
            ]);

            // Generate the paid invoice / receipt and email it to the customer.
            $this->generateInvoice($subscription);

            // WHMCS sync
            if ($this->whmcs->isConfigured() && get_setting('whmcs_sync_enabled', 0)) {
                $this->syncToWhmcs($user, $subscription);
            }

            // Send welcome email with credentials
            $credentials = $this->generateCredentials($user);
            Mail::to($user->email)->queue(new WelcomeWithCredentialsMail($user, $subscription, $credentials));

            return true;
        } catch (\Exception $e) {
            \Log::error('IPTV provisioning failed for subscription ' . $subscription->id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove the Xtream line and terminate the WHMCS service for a subscription.
     * Called before a subscription record is deleted so no orphan account is left
     * on the streaming panel.
     */
    public function delete(UserSubscription $subscription): bool
    {
        try {
            if ($subscription->xtream_username) {
                $this->xtream->deleteLine($subscription->xtream_username);
            }

            if ($this->whmcs->isConfigured() && $subscription->whmcs_service_id) {
                $this->whmcs->terminateService((int) $subscription->whmcs_service_id);
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('IPTV delete failed for subscription ' . $subscription->id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create + email the invoice receipt for a subscription, once.
     * A failure here must not abort provisioning.
     */
    private function generateInvoice(UserSubscription $subscription): void
    {
        if ($subscription->invoice_id) {
            return;
        }

        try {
            $invoice = $this->invoices->createForSubscription($subscription, [
                'amount'       => $subscription->amount ?? $subscription->plan->price ?? 0,
                'total_amount' => $subscription->amount ?? $subscription->plan->price ?? 0,
            ]);
            $this->invoices->sendByEmail($invoice);
        } catch (\Exception $e) {
            \Log::error('Invoice generation failed for subscription ' . $subscription->id . ': ' . $e->getMessage());
        }
    }

    public function suspend(UserSubscription $subscription): bool
    {
        if (!$subscription->xtream_username) {
            return true;
        }

        try {
            $this->xtream->banLine($subscription->xtream_username);

            if ($this->whmcs->isConfigured()) {
                if ($subscription->whmcs_service_id) {
                    $this->whmcs->suspendService((int) $subscription->whmcs_service_id, 'Subscription suspended');
                } elseif ($subscription->user->whmcs_client_id) {
                    $this->whmcs->syncSubscriptionStatus($subscription->user->whmcs_client_id, false);
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function reactivate(UserSubscription $subscription): bool
    {
        if (!$subscription->xtream_username) {
            return $this->provision($subscription);
        }

        try {
            $this->xtream->unbanLine($subscription->xtream_username);
            $this->xtream->updateLine($subscription->xtream_username, [
                'exp_date' => $subscription->expires_at?->timestamp,
            ]);

            if ($this->whmcs->isConfigured()) {
                if ($subscription->whmcs_service_id) {
                    $this->whmcs->unsuspendService((int) $subscription->whmcs_service_id);
                } elseif ($subscription->user->whmcs_client_id) {
                    $this->whmcs->syncSubscriptionStatus($subscription->user->whmcs_client_id, true);
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function expire(UserSubscription $subscription): bool
    {
        $subscription->update([
            'status'            => 'expired',
            'expiry_alert_sent' => true,
        ]);

        return $this->suspend($subscription);
    }

    public function generateCredentials(User $user): array
    {
        return [
            'username' => $user->xtream_username ?? '',
            'password' => $user->xtream_password ?? '',
            'm3u_url'  => $user->xtream_username
                ? $this->xtream->getM3UUrl($user->xtream_username, $user->xtream_password)
                : '',
            'epg_url'  => $user->xtream_username
                ? $this->xtream->getEpgUrl($user->xtream_username, $user->xtream_password)
                : '',
        ];
    }

    /**
     * Full two-way WHMCS sync for a newly provisioned subscription:
     * ensure client → place + accept order (creates service + invoice) →
     * mark invoice paid. IDs are stored on the subscription for later
     * suspend/terminate and inbound webhook matching.
     */
    private function syncToWhmcs(User $user, UserSubscription $subscription): void
    {
        // 1. Ensure a WHMCS client exists.
        $clientId = $this->ensureWhmcsClient($user);
        if (!$clientId) {
            return;
        }

        // Nothing more to do without a product mapping, or if already ordered.
        $productId = (int) get_setting('whmcs_product_id', 0);
        if ($productId <= 0 || $subscription->whmcs_order_id) {
            return;
        }

        // 2. Place the order for the IPTV product.
        $order = $this->whmcs->addOrder([
            'clientid'        => $clientId,
            'pid'             => $productId,
            'billingcycle'    => 'onetime',
            'paymentmethod'   => 'stripe',
            'noinvoiceemail'  => true,
        ]);

        if (($order['result'] ?? '') !== 'success') {
            return;
        }

        $orderId   = $order['orderid'] ?? null;
        $serviceId = $this->firstId($order['productids'] ?? null);
        $invoiceId = $order['invoiceid'] ?? null;

        // 3. Accept the order so WHMCS provisions the module.
        if ($orderId) {
            $this->whmcs->acceptOrder((int) $orderId, ['sendemail' => false]);
        }

        // 4. Record the payment against the generated invoice.
        if ($invoiceId) {
            $this->whmcs->addInvoicePayment(
                (int) $invoiceId,
                $subscription->transaction_id ?? ('SUB-' . $subscription->id),
                (float) ($subscription->amount ?? 0),
                'stripe'
            );
        }

        $subscription->update([
            'whmcs_order_id'   => $orderId,
            'whmcs_service_id' => $serviceId,
            'whmcs_invoice_id' => $invoiceId,
        ]);
    }

    private function ensureWhmcsClient(User $user): ?int
    {
        if ($user->whmcs_client_id) {
            $this->whmcs->updateClient($user->whmcs_client_id, [
                'email'     => $user->email,
                'firstname' => $user->name,
            ]);
            return (int) $user->whmcs_client_id;
        }

        $result = $this->whmcs->createClient([
            'firstname'   => $user->name,
            'lastname'    => '',
            'email'       => $user->email,
            'address1'    => 'N/A',
            'city'        => 'N/A',
            'state'       => 'N/A',
            'postcode'    => '0000',
            'country'     => 'US',
            'phonenumber' => $user->phone ?? '',
            'password2'   => Str::random(16),
        ]);

        if (($result['result'] ?? '') === 'success' && isset($result['clientid'])) {
            $user->update(['whmcs_client_id' => $result['clientid']]);
            return (int) $result['clientid'];
        }

        return null;
    }

    /**
     * WHMCS returns productids as a comma-separated string (e.g. "12,13").
     */
    private function firstId(mixed $ids): ?int
    {
        if (is_array($ids)) {
            $ids = reset($ids);
        }
        if (is_string($ids) && $ids !== '') {
            $ids = explode(',', $ids)[0];
        }
        return is_numeric($ids) ? (int) $ids : null;
    }
}
