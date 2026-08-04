<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Jobs\ProvisionSubscriptionJob;
use App\Models\SubscriptionRenewal;
use App\Models\UserSubscription;
use App\Services\StripeService;
use App\Services\SubscriptionRenewalService;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, SubscriptionRenewalService $renewals)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $stripe = new StripeService();
            $event  = $stripe->constructWebhookEvent($payload, $sigHeader);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Webhook error'], 400);
        }

        match ($event->type) {
            'payment_intent.succeeded'      => $this->handlePaymentIntentSucceeded($event->data->object, $renewals),
            'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($event->data->object, $renewals),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }

    private function handlePaymentIntentSucceeded(
        \Stripe\PaymentIntent $intent,
        SubscriptionRenewalService $renewals
    ): void {
        // Renewals are tracked on their own rows, so check those first.
        $renewal = SubscriptionRenewal::where('stripe_payment_intent_id', $intent->id)->first();

        if ($renewal) {
            $renewals->markPaid($renewal, ['stripe_charge_id' => $intent->latest_charge]);
            return;
        }

        $subscription = UserSubscription::where('stripe_payment_intent_id', $intent->id)
            ->where('status', 'pending')
            ->first();

        if (!$subscription) {
            return;
        }

        $subscription->update([
            'status'           => 'active',
            'stripe_charge_id' => $intent->latest_charge,
            'starts_at'        => now(),
            'expires_at'       => now()->addDays($subscription->plan->duration_days),
        ]);

        if (get_setting('iptv_provisioning_enabled', 0)) {
            dispatch(new ProvisionSubscriptionJob($subscription));
        }
    }

    private function handlePaymentIntentFailed(
        \Stripe\PaymentIntent $intent,
        SubscriptionRenewalService $renewals
    ): void {
        $renewal = SubscriptionRenewal::where('stripe_payment_intent_id', $intent->id)->first();

        if ($renewal) {
            $renewals->markFailed($renewal, $intent->last_payment_error->message ?? 'Card payment failed.');
            return;
        }

        UserSubscription::where('stripe_payment_intent_id', $intent->id)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);
    }
}
