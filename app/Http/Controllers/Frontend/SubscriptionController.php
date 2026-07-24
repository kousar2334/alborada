<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PaymentLink;
use App\Models\PricingPlan;
use App\Models\UserSubscription;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    /**
     * MAG plans require a MAC address at checkout; M3U plans do not.
     * Returns the validated MAC (or null for non-MAG plans).
     */
    private function macForPlan(Request $request, PricingPlan $plan): ?string
    {
        if ($plan->iptv_device_type !== 'mag') {
            return null;
        }

        $data = $request->validate([
            'mac_address' => ['required', 'string', 'regex:/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/'],
        ], [
            'mac_address.required' => __tr('A MAG device MAC address is required for this plan.'),
            'mac_address.regex'    => __tr('Enter a valid MAC address (e.g. 00:1A:79:12:34:56).'),
        ]);

        return strtoupper($data['mac_address']);
    }

    /**
     * Show the subscription confirmation / payment method selection page.
     */
    public function confirm(int $planId)
    {
        $plan = PricingPlan::where('id', $planId)
            ->where('status', config('settings.general_status.active'))
            ->firstOrFail();

        // Free plans don't need a confirmation page
        if ($plan->price <= 0) {
            return redirect()->route('membership.buy.free', ['membership_id' => $plan->id]);
        }

        $user = Auth::user();

        $activeSubscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($activeSubscription) {
            return redirect()->route('member.subscriptions')
                ->with('error', 'You already have an active subscription expiring on ' . $activeSubscription->expires_at->format('M d, Y') . '.');
        }

        $stripeEnabled   = (bool) get_setting('stripe_enabled', 0);
        $stripePublicKey = get_setting('stripe_public_key', config('services.stripe.key', ''));

        $bankTransferEnabled      = (bool) get_setting('bank_transfer_enabled', 0);
        $bankTransferInstructions = get_setting('bank_transfer_instructions', '');

        return view('frontend.pages.member.subscription-confirm', compact(
            'plan',
            'stripeEnabled',
            'stripePublicKey',
            'bankTransferEnabled',
            'bankTransferInstructions'
        ));
    }

    /**
     * Record a bank-transfer payment: creates a pending subscription with the
     * customer's reference and uploaded slip. An admin reviews and approves it
     * from the backend, which triggers provisioning + receipt.
     */
    public function bankTransfer(Request $request)
    {
        $request->validate([
            'membership_id'          => 'required|integer|exists:pricing_plans,id',
            'bank_transaction_number' => 'required|string|max:191',
            'bank_slip'              => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if (!get_setting('bank_transfer_enabled', 0)) {
            return back()->with('error', __tr('Bank transfer is not available.'));
        }

        $plan = PricingPlan::where('id', $request->membership_id)
            ->where('status', config('settings.general_status.active'))
            ->firstOrFail();

        $user = Auth::user();

        $activeSubscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($activeSubscription) {
            return back()->with('error', __tr('You already have an active subscription.'));
        }

        $mac = $this->macForPlan($request, $plan);

        try {
            DB::beginTransaction();

            $slipPath = $request->file('bank_slip')->store('bank-slips', 'public');

            UserSubscription::create([
                'user_id'                 => $user->id,
                'plan_id'                 => $plan->id,
                'transaction_id'          => 'BANK-' . strtoupper(Str::random(12)),
                'amount'                  => $plan->effective_price,
                'payment_method'          => 'bank_transfer',
                'status'                  => 'pending',
                'bank_transaction_number' => $request->bank_transaction_number,
                'bank_slip'               => $slipPath,
                'iptv_mac'                => $mac,
                'iptv_device_type'        => $plan->iptv_device_type ?? 'm3u',
            ]);

            DB::commit();

            return redirect()->route('member.subscriptions')
                ->with('success', __tr('Your bank transfer was submitted and is pending review. You will be notified once approved.'));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bank transfer submission failed: ' . $e->getMessage());
            return back()->with('error', __tr('Failed to submit bank transfer. Please try again.'));
        }
    }

    /**
     * Activate a free/trial plan directly.
     */
    public function buy(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|integer|exists:pricing_plans,id',
        ]);

        $plan = PricingPlan::where('id', $request->membership_id)
            ->where('status', config('settings.general_status.active'))
            ->firstOrFail();

        if ($plan->price > 0) {
            return back()->with('error', 'This plan requires payment. Please use the payment option.');
        }

        $user = Auth::user();

        $activeSubscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($activeSubscription) {
            return back()->with('error', 'You already have an active subscription. It expires on ' . $activeSubscription->expires_at->format('M d, Y') . '.');
        }

        try {
            DB::beginTransaction();

            UserSubscription::create([
                'user_id'          => $user->id,
                'plan_id'          => $plan->id,
                'transaction_id'   => 'TRIAL-' . strtoupper(Str::random(12)),
                'amount'           => 0,
                'payment_method'   => 'trial',
                'status'           => 'active',
                'starts_at'        => now(),
                'expires_at'       => now()->addDays($plan->duration_days),
                'iptv_mac'         => $request->input('mac_address'),
                'iptv_device_type' => $plan->iptv_device_type ?? 'm3u',
            ]);

            DB::commit();

            return redirect()->route('member.subscriptions')
                ->with('success', 'You have successfully activated the ' . $plan->title . ' plan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to activate plan. Please try again.');
        }
    }

    /**
     * Create a Stripe PaymentIntent and return the client_secret for Stripe.js.
     */
    public function initiateStripePayment(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|integer|exists:pricing_plans,id',
        ]);

        if (!get_setting('stripe_enabled', 0)) {
            return response()->json(['error' => 'Stripe payment is not available.'], 422);
        }

        $plan = PricingPlan::where('id', $request->membership_id)
            ->where('status', config('settings.general_status.active'))
            ->firstOrFail();

        if ($plan->price <= 0) {
            return response()->json(['error' => 'This is a free plan.'], 422);
        }

        $user = Auth::user();

        $activeSubscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($activeSubscription) {
            return response()->json(['error' => 'You already have an active subscription.'], 422);
        }

        $secretKey = get_setting('stripe_secret_key');
        if (empty($secretKey)) {
            return response()->json(['error' => 'Stripe is not configured. Please contact the administrator.'], 422);
        }

        try {
            $mac = $this->macForPlan($request, $plan);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()->first()], 422);
        }

        try {
            $stripe = new StripeService();
            $intentData = $stripe->createPaymentIntent($plan, $user);

            $transactionId = 'STRIPE-' . strtoupper(Str::random(14));

            $subscription = UserSubscription::create([
                'user_id'                  => $user->id,
                'plan_id'                  => $plan->id,
                'transaction_id'           => $transactionId,
                'amount'                   => $plan->effective_price,
                'payment_method'           => 'stripe',
                'status'                   => 'pending',
                'stripe_payment_intent_id' => $intentData['payment_intent_id'],
                'iptv_mac'                 => $mac,
                'iptv_device_type'         => $plan->iptv_device_type ?? 'm3u',
            ]);

            return response()->json([
                'client_secret'   => $intentData['client_secret'],
                'subscription_id' => $subscription->id,
            ]);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            \Log::error('Stripe auth error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid Stripe API key. Please contact the administrator.'], 422);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe API error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            \Log::error('Stripe payment initiation error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to initiate payment. Please try again.'], 500);
        }
    }

    /**
     * Confirm Stripe payment after Stripe.js confirmPayment() succeeds.
     */
    public function stripeSuccess(Request $request)
    {
        $request->validate([
            'payment_intent' => 'required|string',
        ]);

        try {
            $stripe = new StripeService();
            $intent = $stripe->retrievePaymentIntent($request->payment_intent);

            $subscription = UserSubscription::where('stripe_payment_intent_id', $intent->id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$subscription) {
                return redirect()->route('member.subscriptions')
                    ->with('error', 'Subscription not found.');
            }

            if ($intent->status === 'succeeded' && $subscription->status === 'pending') {
                $subscription->update([
                    'status'           => 'active',
                    'stripe_charge_id' => $intent->latest_charge,
                    'starts_at'        => now(),
                    'expires_at'       => now()->addDays($subscription->plan->duration_days),
                ]);

                // Provision here too — the browser may return before the webhook
                // fires. ProvisionSubscriptionJob is idempotent (guards on an
                // existing iptv_user_id), so webhook + return won't double-create.
                if (get_setting('iptv_provisioning_enabled', 0) && empty($subscription->iptv_user_id)) {
                    dispatch(new \App\Jobs\ProvisionSubscriptionJob($subscription));
                }

                return redirect()->route('member.subscriptions')
                    ->with('success', 'Payment successful! Your subscription is now active.');
            }

            return redirect()->route('member.subscriptions')
                ->with('error', 'Payment could not be verified. Please contact support.');
        } catch (\Exception $e) {
            return redirect()->route('member.subscriptions')
                ->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    /**
     * Redirect a payment link token to the subscription confirmation page.
     */
    public function paymentLinkRedirect(string $token)
    {
        $link = PaymentLink::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        if (!Auth::check()) {
            session(['payment_link_token' => $token]);
            return redirect()->route('member.login')
                ->with('info', 'Please log in to complete your payment.');
        }

        // Mark as used
        $link->update(['used_at' => now()]);

        return redirect()->route('subscription.confirm', $link->plan_id);
    }

    /**
     * Show user's subscription history.
     */
    public function mySubscriptions()
    {
        $subscriptions = UserSubscription::with('plan')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $activeSubscription = UserSubscription::with('plan')
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        return view('frontend.pages.member.subscriptions', compact('subscriptions', 'activeSubscription'));
    }
}
