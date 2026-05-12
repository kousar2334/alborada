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

        return view('frontend.pages.member.subscription-confirm', compact(
            'plan',
            'stripeEnabled',
            'stripePublicKey'
        ));
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
                'user_id'        => $user->id,
                'plan_id'        => $plan->id,
                'transaction_id' => 'TRIAL-' . strtoupper(Str::random(12)),
                'amount'         => 0,
                'payment_method' => 'trial',
                'status'         => 'active',
                'starts_at'      => now(),
                'expires_at'     => now()->addDays($plan->duration_days),
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

        try {
            $stripe = new StripeService();
            $intentData = $stripe->createPaymentIntent($plan, $user);

            // Create a pending subscription record
            $transactionId = 'STRIPE-' . strtoupper(Str::random(14));

            $subscription = UserSubscription::create([
                'user_id'                  => $user->id,
                'plan_id'                  => $plan->id,
                'transaction_id'           => $transactionId,
                'amount'                   => $plan->price,
                'payment_method'           => 'stripe',
                'status'                   => 'pending',
                'stripe_payment_intent_id' => $intentData['payment_intent_id'],
            ]);

            return response()->json([
                'client_secret'   => $intentData['client_secret'],
                'subscription_id' => $subscription->id,
            ]);
        } catch (\Exception $e) {
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
