<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Services\IptvProvisioningService;
use Illuminate\Http\Request;

/**
 * Inbound WHMCS → app webhook. Configure a WHMCS action hook (or the
 * "URL Callback" of a hook script) to POST events here. Requests are
 * authenticated with an HMAC-SHA256 of the raw body using the shared
 * secret stored in settings (`whmcs_webhook_secret`).
 *
 * Expected JSON body:
 *   { "event": "invoice_paid|service_suspended|service_unsuspended|service_terminated",
 *     "service_id": 123, "invoice_id": 456 }
 */
class WhmcsWebhookController extends Controller
{
    public function handle(Request $request, IptvProvisioningService $provisioning)
    {
        $secret = (string) get_setting('whmcs_webhook_secret', '');
        if ($secret === '') {
            return response()->json(['error' => 'Webhook not configured'], 403);
        }

        $expected  = hash_hmac('sha256', $request->getContent(), $secret);
        $signature = (string) $request->header('X-WHMCS-Signature', '');
        if (!hash_equals($expected, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event     = (string) $request->input('event', '');
        $serviceId = $request->input('service_id');
        $invoiceId = $request->input('invoice_id');

        $subscription = null;
        if ($serviceId) {
            $subscription = UserSubscription::where('whmcs_service_id', $serviceId)->first();
        }
        if (!$subscription && $invoiceId) {
            $subscription = UserSubscription::where('whmcs_invoice_id', $invoiceId)->first();
        }

        if (!$subscription) {
            return response()->json(['status' => 'ignored', 'reason' => 'no matching subscription']);
        }

        switch ($event) {
            case 'invoice_paid':
                if ($subscription->status === 'pending') {
                    $subscription->update([
                        'status'     => 'active',
                        'starts_at'  => $subscription->starts_at ?? now(),
                        'expires_at' => $subscription->expires_at
                            ?? now()->addDays($subscription->plan->duration_days ?? 30),
                    ]);
                    if (get_setting('iptv_provisioning_enabled', 0) && empty($subscription->iptv_user_id)) {
                        dispatch(new \App\Jobs\ProvisionSubscriptionJob($subscription));
                    }
                }
                break;

            case 'service_suspended':
                $provisioning->suspend($subscription);
                break;

            case 'service_unsuspended':
                $provisioning->reactivate($subscription);
                break;

            case 'service_terminated':
                $subscription->update(['status' => 'cancelled']);
                $provisioning->suspend($subscription);
                break;

            default:
                return response()->json(['status' => 'ignored', 'reason' => 'unknown event']);
        }

        return response()->json(['status' => 'ok']);
    }
}
