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
use Illuminate\Support\Facades\Storage;
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

        $sslEnabled          = (bool) get_setting('ssl_enabled', 0);
        $bankTransferEnabled = (bool) get_setting('bank_transfer_enabled', 0);
        $stripeEnabled       = (bool) get_setting('stripe_enabled', 0);
        $stripePublicKey     = get_setting('stripe_public_key', config('services.stripe.key', ''));

        return view('frontend.pages.member.subscription-confirm', compact(
            'plan',
            'sslEnabled',
            'bankTransferEnabled',
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
     * Handle bank transfer payment submission.
     */
    public function bankPayment(Request $request)
    {
        $request->validate([
            'membership_id'          => 'required|integer|exists:pricing_plans,id',
            'bank_transaction_number' => 'required|string|max:200',
            'bank_slip'              => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        if (!get_setting('bank_transfer_enabled', 0)) {
            return back()->with('error', 'Bank transfer payment is not available at this time.');
        }

        $plan = PricingPlan::where('id', $request->membership_id)
            ->where('status', config('settings.general_status.active'))
            ->firstOrFail();

        if ($plan->price <= 0) {
            return back()->with('error', 'This is a free plan. No payment required.');
        }

        $user = Auth::user();

        $activeSubscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($activeSubscription) {
            return back()->with('error', 'You already have an active subscription.');
        }

        try {
            DB::beginTransaction();

            // Store the bank slip
            $slipPath = $request->file('bank_slip')->store('bank-slips', 'public');

            $transactionId = 'BANK-' . strtoupper(Str::random(14));

            UserSubscription::create([
                'user_id'                 => $user->id,
                'plan_id'                 => $plan->id,
                'transaction_id'          => $transactionId,
                'amount'                  => $plan->price,
                'payment_method'          => 'bank_transfer',
                'status'                  => 'pending',
                'bank_transaction_number' => $request->bank_transaction_number,
                'bank_slip'               => $slipPath,
                'starts_at'               => null,
                'expires_at'              => null,
            ]);

            DB::commit();

            return redirect()->route('member.subscriptions')
                ->with('success', 'Your payment slip has been submitted. Your subscription will be activated after admin verification.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit payment. Please try again.');
        }
    }

    /**
     * Initiate SSLCommerz payment for a paid plan.
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|integer|exists:pricing_plans,id',
        ]);

        if (!get_setting('ssl_enabled', 0)) {
            return back()->with('error', 'Online payment is not available at this time.');
        }

        $plan = PricingPlan::where('id', $request->membership_id)
            ->where('status', config('settings.general_status.active'))
            ->firstOrFail();

        if ($plan->price <= 0) {
            return back()->with('error', 'This is a free plan. No payment required.');
        }

        $user = Auth::user();

        $activeSubscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($activeSubscription) {
            return back()->with('error', 'You already have an active subscription. It expires on ' . $activeSubscription->expires_at->format('M d, Y') . '.');
        }

        // Read credentials from settings (admin-configurable)
        $storeId   = get_setting('sslcommerz_store_id', config('sslcommerz.store_id'));
        $storePass = get_setting('sslcommerz_store_password', config('sslcommerz.store_password'));
        $isSandbox = (bool) get_setting('sslcommerz_sandbox', config('sslcommerz.sandbox', true));
        $currency  = get_setting('sslcommerz_currency', config('sslcommerz.currency', 'BDT'));

        try {
            DB::beginTransaction();

            $transactionId = 'TXN-' . strtoupper(Str::random(16));

            $subscription = UserSubscription::create([
                'user_id'        => $user->id,
                'plan_id'        => $plan->id,
                'transaction_id' => $transactionId,
                'amount'         => $plan->price,
                'payment_method' => 'sslcommerz',
                'status'         => 'pending',
                'starts_at'      => null,
                'expires_at'     => null,
            ]);

            DB::commit();

            $sslApiUrl = $isSandbox
                ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
                : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';

            $postData = [
                'store_id'         => $storeId,
                'store_passwd'     => $storePass,
                'total_amount'     => $plan->price,
                'currency'         => $currency,
                'tran_id'          => $transactionId,
                'success_url'      => route('subscription.ssl.success'),
                'fail_url'         => route('subscription.ssl.fail'),
                'cancel_url'       => route('subscription.ssl.cancel'),
                'ipn_url'          => route('subscription.ssl.ipn'),
                'cus_name'         => $user->name,
                'cus_email'        => $user->email,
                'cus_add1'         => 'N/A',
                'cus_city'         => 'N/A',
                'cus_country'      => 'Bangladesh',
                'cus_phone'        => $user->phone ?? '01700000000',
                'product_name'     => $plan->title . ' Subscription',
                'product_category' => 'Subscription',
                'product_profile'  => 'general',
                'shipping_method'  => 'NO',
                'num_of_item'      => 1,
                'weight_of_items'  => 0,
                'ship_name'        => $user->name,
                'ship_add1'        => 'N/A',
                'ship_city'        => 'N/A',
                'ship_country'     => 'Bangladesh',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $sslApiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($ch);
            curl_close($ch);

            $sslData = json_decode($response, true);

            if (isset($sslData['GatewayPageURL']) && $sslData['GatewayPageURL'] != '') {
                $subscription->ssl_session_key = $sslData['sessionkey'] ?? null;
                $subscription->save();

                return redirect($sslData['GatewayPageURL']);
            }

            $subscription->update(['status' => 'failed']);

            return back()->with('error', 'Payment initiation failed. Please try again.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Handle SSLCommerz success callback.
     */
    public function sslSuccess(Request $request)
    {
        $tranId = $request->tran_id;

        $subscription = UserSubscription::where('transaction_id', $tranId)->first();

        if (!$subscription) {
            return redirect()->route('member.subscriptions')
                ->with('error', 'Subscription not found.');
        }

        $validated = $this->validateSslPayment($request->val_id);

        if ($validated && $validated['status'] === 'VALID') {
            $subscription->update([
                'status'     => 'active',
                'ssl_val_id' => $request->val_id,
                'starts_at'  => now(),
                'expires_at' => now()->addDays($subscription->plan->duration_days),
            ]);

            return redirect()->route('member.subscriptions')
                ->with('success', 'Payment successful! Your subscription is now active.');
        }

        $subscription->update(['status' => 'failed']);

        return redirect()->route('member.subscriptions')
            ->with('error', 'Payment validation failed. Please contact support.');
    }

    /**
     * Handle SSLCommerz fail callback.
     */
    public function sslFail(Request $request)
    {
        $tranId = $request->tran_id;

        if ($tranId) {
            UserSubscription::where('transaction_id', $tranId)
                ->where('status', 'pending')
                ->update(['status' => 'failed']);
        }

        return redirect()->route('pricing.plans')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Handle SSLCommerz cancel callback.
     */
    public function sslCancel(Request $request)
    {
        $tranId = $request->tran_id;

        if ($tranId) {
            UserSubscription::where('transaction_id', $tranId)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);
        }

        return redirect()->route('pricing.plans')
            ->with('error', 'Payment was cancelled.');
    }

    /**
     * Handle SSLCommerz IPN.
     */
    public function sslIpn(Request $request)
    {
        $tranId = $request->tran_id;

        if (!$tranId) {
            return response()->json(['status' => 'error', 'message' => 'Transaction ID missing'], 400);
        }

        $subscription = UserSubscription::where('transaction_id', $tranId)->first();

        if (!$subscription) {
            return response()->json(['status' => 'error', 'message' => 'Subscription not found'], 404);
        }

        $validated = $this->validateSslPayment($request->val_id);

        if ($validated && $validated['status'] === 'VALID') {
            if ($subscription->status === 'pending') {
                $subscription->update([
                    'status'     => 'active',
                    'ssl_val_id' => $request->val_id,
                    'starts_at'  => now(),
                    'expires_at' => now()->addDays($subscription->plan->duration_days),
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
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

    /**
     * Validate payment with SSLCommerz validation API.
     */
    private function validateSslPayment(string $valId): ?array
    {
        $storeId   = get_setting('sslcommerz_store_id', config('sslcommerz.store_id'));
        $storePass = get_setting('sslcommerz_store_password', config('sslcommerz.store_password'));
        $isSandbox = (bool) get_setting('sslcommerz_sandbox', config('sslcommerz.sandbox', true));

        $validateUrl = $isSandbox
            ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
            : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';

        $validateUrl .= '?val_id=' . urlencode($valId)
            . '&store_id=' . urlencode($storeId)
            . '&store_passwd=' . urlencode($storePass)
            . '&format=json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $validateUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }

        return json_decode($response, true);
    }
}
