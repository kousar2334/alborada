<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProvisionSubscriptionJob;
use App\Models\PricingPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\IptvProvisioningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResellerController extends Controller
{
    public function listClients(Request $request): JsonResponse
    {
        $reseller = Auth::user();

        $clients = $reseller->resellerClients()
            ->with(['subscriptions' => fn($q) => $q->where('status', 'active')->with('plan')])
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $clients]);
    }

    public function createClient(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'plan_id'  => 'required|integer|exists:pricing_plans,id',
            'password' => 'nullable|string|min:8',
        ]);

        $reseller = Auth::user();

        $plan = PricingPlan::where('id', $request->plan_id)
            ->where('status', config('settings.general_status.active', 1))
            ->firstOrFail();

        $cost = $plan->price * (1 - ($reseller->markup_percentage / 100));

        if ($reseller->credits < $cost) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient credits. Required: $' . number_format($cost, 2) . ', Available: $' . number_format($reseller->credits, 2),
            ], 422);
        }

        $client = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password ?? Str::random(12)),
            'type'        => config('settings.user_type.customer', 2),
            'status'      => 1,
            'reseller_id' => $reseller->id,
        ]);

        $subscription = UserSubscription::create([
            'user_id'        => $client->id,
            'plan_id'        => $plan->id,
            'transaction_id' => 'RESELLER-' . strtoupper(Str::random(12)),
            'amount'         => $plan->price,
            'payment_method' => 'trial',
            'status'         => 'active',
            'starts_at'      => now(),
            'expires_at'     => now()->addDays($plan->duration_days),
        ]);

        $reseller->deductCredits($cost, 'Client: ' . $client->email . ' — ' . $plan->title, $client->id);

        if (get_setting('iptv_provisioning_enabled', 0)) {
            dispatch(new ProvisionSubscriptionJob($subscription));
        }

        return response()->json([
            'success' => true,
            'message' => 'Client created successfully.',
            'client'  => ['id' => $client->id, 'email' => $client->email],
        ], 201);
    }

    public function suspendClient(Request $request): JsonResponse
    {
        $request->validate(['client_id' => 'required|integer|exists:users,id']);

        $client = User::where('id', $request->client_id)
            ->where('reseller_id', Auth::id())
            ->firstOrFail();

        $subscription = $client->subscriptions()->where('status', 'active')->first();

        if ($subscription && get_setting('iptv_provisioning_enabled', 0)) {
            app(IptvProvisioningService::class)->suspend($subscription);
        }

        $subscription?->update(['status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Client suspended.']);
    }

    public function reactivateClient(Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => 'required|integer|exists:users,id',
            'plan_id'   => 'nullable|integer|exists:pricing_plans,id',
        ]);

        $client = User::where('id', $request->client_id)
            ->where('reseller_id', Auth::id())
            ->firstOrFail();

        $subscription = $client->subscriptions()->whereIn('status', ['cancelled', 'expired'])->latest()->first();

        if ($subscription && get_setting('iptv_provisioning_enabled', 0)) {
            $subscription->update([
                'status'     => 'active',
                'starts_at'  => now(),
                'expires_at' => now()->addDays($subscription->plan->duration_days),
            ]);
            app(IptvProvisioningService::class)->reactivate($subscription);
        }

        return response()->json(['success' => true, 'message' => 'Client reactivated.']);
    }

    public function creditBalance(Request $request): JsonResponse
    {
        $reseller = Auth::user();
        return response()->json([
            'success'  => true,
            'credits'  => $reseller->credits,
            'currency' => 'USD',
        ]);
    }

    public function availablePlans(Request $request): JsonResponse
    {
        $plans = PricingPlan::where('status', config('settings.general_status.active', 1))
            ->orderBy('sort_order')
            ->get(['id', 'title', 'duration_days', 'price', 'max_connections', 'streaming_quality', 'catchup_days', 'dvr_enabled', 'is_trial']);

        return response()->json(['success' => true, 'data' => $plans]);
    }
}
