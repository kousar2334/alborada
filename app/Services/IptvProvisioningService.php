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
    ) {}

    public function provision(UserSubscription $subscription): bool
    {
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

            // WHMCS sync
            if ($this->whmcs->isConfigured() && get_setting('whmcs_sync_enabled', 0)) {
                $this->syncToWhmcs($user, $subscription);
            }

            // Send welcome email with credentials
            $credentials = $this->generateCredentials($user);
            Mail::to($user->email)->queue(new WelcomeWithCredentialsMail($user, $subscription, $credentials));

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function suspend(UserSubscription $subscription): bool
    {
        if (!$subscription->xtream_username) {
            return true;
        }

        try {
            $this->xtream->banLine($subscription->xtream_username);

            if ($this->whmcs->isConfigured() && $subscription->user->whmcs_client_id) {
                $this->whmcs->syncSubscriptionStatus($subscription->user->whmcs_client_id, false);
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

            if ($this->whmcs->isConfigured() && $subscription->user->whmcs_client_id) {
                $this->whmcs->syncSubscriptionStatus($subscription->user->whmcs_client_id, true);
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

    private function syncToWhmcs(User $user, UserSubscription $subscription): void
    {
        if ($user->whmcs_client_id) {
            $this->whmcs->updateClient($user->whmcs_client_id, [
                'email'    => $user->email,
                'firstname' => $user->name,
            ]);
            return;
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
        }
    }
}
