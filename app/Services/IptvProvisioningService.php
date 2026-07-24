<?php

namespace App\Services;

use App\Contracts\IptvProvider;
use App\Mail\WelcomeWithCredentialsMail;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Mail;

class IptvProvisioningService
{
    public function __construct(
        protected IptvProvider $provider,
        protected WhmcsService $whmcs,
        protected InvoiceService $invoices,
    ) {}

    /**
     * Whether WHMCS billing sync should run. Both configured AND enabled — the
     * `whmcs_sync_enabled` toggle must fully disable every WHMCS side effect.
     */
    protected function whmcsEnabled(): bool
    {
        return $this->whmcs->isConfigured() && (bool) get_setting('whmcs_sync_enabled', 0);
    }

    public function provision(UserSubscription $subscription): bool
    {
        // No active streaming provider — nothing to provision.
        if (get_setting('active_iptv_provider', 'xtream') === 'none' || !$this->provider->isConfigured()) {
            return false;
        }

        // Idempotency guard: if this subscription already has a provisioned
        // account, don't create a duplicate on the panel. The Stripe webhook and
        // the browser-return path can both fire for the same payment.
        if ($subscription->iptv_user_id) {
            return true;
        }

        $user = $subscription->user;

        try {
            $account = $this->provider->createAccount($subscription);

            if (empty($account)) {
                \Log::error('IPTV provisioning returned no account for subscription ' . $subscription->id);
                return false;
            }

            // Store the normalised account on the subscription and mirror the
            // credentials onto the user record.
            $subscription->update([
                'iptv_provider'    => $this->provider->key(),
                'iptv_user_id'     => $account['user_id'] ?? null,
                'iptv_username'    => $account['username'] ?? null,
                'iptv_password'    => $account['password'] ?? null,
                'iptv_mac'         => $account['mac'] ?? $subscription->iptv_mac,
                'iptv_m3u_url'     => $account['m3u_url'] ?? null,
                'iptv_device_type' => $account['device_type'] ?? 'm3u',
            ]);

            $user->update([
                'iptv_username' => $account['username'] ?? null,
                'iptv_password' => $account['password'] ?? null,
            ]);

            // Generate the paid invoice / receipt and email it to the customer.
            $this->generateInvoice($subscription);

            // WHMCS sync
            if ($this->whmcsEnabled()) {
                $this->syncToWhmcs($user, $subscription);
            }

            // Send welcome email with credentials
            $credentials = $this->generateCredentials($subscription->fresh());
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
            if ($subscription->iptv_user_id || $subscription->iptv_username) {
                $this->provider->deleteAccount($subscription);
            }

            if ($this->whmcsEnabled() && $subscription->whmcs_service_id) {
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
        if (!$subscription->iptv_user_id && !$subscription->iptv_username) {
            return true;
        }

        try {
            $this->provider->disable($subscription);

            if ($this->whmcsEnabled()) {
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
        if (!$subscription->iptv_user_id && !$subscription->iptv_username) {
            return $this->provision($subscription);
        }

        try {
            $this->provider->enable($subscription);
            $this->provider->renew($subscription, $subscription->plan?->iptvMonths() ?? 1);

            if ($this->whmcsEnabled()) {
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

    /**
     * Extend an existing streaming account after a renewal payment. Provisions
     * from scratch if the subscription was never set up on a panel.
     */
    public function renew(UserSubscription $subscription): bool
    {
        if (get_setting('active_iptv_provider', 'xtream') === 'none' || !$this->provider->isConfigured()) {
            return false;
        }

        if (!$subscription->iptv_user_id && !$subscription->iptv_username) {
            return $this->provision($subscription);
        }

        try {
            return $this->provider->renew($subscription, $subscription->plan?->iptvMonths() ?? 1);
        } catch (\Exception $e) {
            \Log::error('IPTV renew failed for subscription ' . $subscription->id . ': ' . $e->getMessage());
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

    /**
     * Normalised credentials for display / email, read from the stored
     * provider-agnostic fields on the subscription.
     */
    public function generateCredentials(UserSubscription $subscription): array
    {
        return [
            'username'    => $subscription->iptv_username ?? '',
            'password'    => $subscription->iptv_password ?? '',
            'mac'         => $subscription->iptv_mac ?? '',
            'm3u_url'     => $subscription->iptv_m3u_url ?? '',
            'server_url'  => $this->serverUrlFromM3U($subscription->iptv_m3u_url ?? ''),
            'device_type' => $subscription->iptv_device_type ?? 'm3u',
        ];
    }

    /**
     * The base "Server URL" (scheme://host[:port]) that Xtream-login apps
     * (IPTV Smarters, TiviMate, XCIPTV) expect, derived from the account's
     * stored M3U URL. Works for both providers: Xtream lines carry the panel
     * host, 8K lines carry 8K's own streaming domain.
     */
    private function serverUrlFromM3U(string $m3uUrl): string
    {
        if ($m3uUrl === '') {
            return '';
        }

        $parts = parse_url($m3uUrl);
        if (empty($parts['scheme']) || empty($parts['host'])) {
            return '';
        }

        return $parts['scheme'] . '://' . $parts['host']
            . (isset($parts['port']) ? ':' . $parts['port'] : '');
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
