<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\ProvisionSubscriptionJob;
use App\Models\PaymentLink;
use App\Models\PricingPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionApproved;
use App\Notifications\SubscriptionRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = UserSubscription::with(['user', 'plan'])->latest();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->input('method'));
        }

        $subscriptions = $query->paginate(20)->withQueryString();

        $stats = [
            'total'   => UserSubscription::count(),
            'active'  => UserSubscription::where('status', 'active')->count(),
            'pending' => UserSubscription::where('status', 'pending')->count(),
            'expired' => UserSubscription::where('status', 'active')->where('expires_at', '<', now())->count(),
        ];

        $members = User::where('type', config('settings.user_type.member'))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $plans = PricingPlan::orderBy('price')
            ->get(['id', 'title', 'price', 'duration_days']);

        return view('backend.modules.subscriptions.index', compact('subscriptions', 'stats', 'members', 'plans'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|integer|exists:users,id',
            'plan_id'    => 'required|integer|exists:pricing_plans,id',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $user = User::findOrFail($request->user_id);
        $plan = PricingPlan::findOrFail($request->plan_id);

        $subscription = UserSubscription::create([
            'user_id'        => $user->id,
            'plan_id'        => $plan->id,
            'transaction_id' => 'ADMIN-' . strtoupper(Str::random(12)),
            'amount'         => $plan->price,
            'payment_method' => 'manual',
            'status'         => 'active',
            'admin_note'     => $request->admin_note ?: __tr('Assigned by admin'),
            'starts_at'      => now(),
            'expires_at'     => now()->addDays($plan->duration_days),
        ]);

        // Mail failure (e.g. SMTP not configured yet) must not undo the assignment
        try {
            $user->notify(new SubscriptionApproved($subscription));
        } catch (\Throwable $e) {
            report($e);
        }

        if (get_setting('iptv_provisioning_enabled', 0)) {
            dispatch(new ProvisionSubscriptionJob($subscription));
        }

        return redirect()->route('admin.subscriptions.list')
            ->with('success', __tr('Subscription assigned to ') . $user->name . __tr(' successfully'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:user_subscriptions,id']);

        $subscription = UserSubscription::with('plan', 'user')->findOrFail($request->id);

        $subscription->update([
            'status'     => 'active',
            'admin_note' => $request->admin_note,
            'starts_at'  => now(),
            'expires_at' => now()->addDays($subscription->plan->duration_days),
        ]);

        if ($subscription->user) {
            $subscription->user->notify(new SubscriptionApproved($subscription));
        }

        if (get_setting('iptv_provisioning_enabled', 0)) {
            dispatch(new ProvisionSubscriptionJob($subscription));
        }

        return redirect()->route('admin.subscriptions.list')
            ->with('success', __tr('Subscription approved and activated successfully'));
    }

    public function reject(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:user_subscriptions,id']);

        $subscription = UserSubscription::with('plan', 'user')->findOrFail($request->id);

        $subscription->update([
            'status'     => 'rejected',
            'admin_note' => $request->admin_note,
        ]);

        if ($subscription->user) {
            $subscription->user->notify(new SubscriptionRejected($subscription));
        }

        return redirect()->route('admin.subscriptions.list')
            ->with('success', __tr('Subscription rejected successfully'));
    }

    public function delete(Request $request, \App\Services\IptvProvisioningService $provisioning)
    {
        $request->validate(['id' => 'required|integer|exists:user_subscriptions,id']);

        $subscription = UserSubscription::findOrFail($request->id);

        // Remove the account from the streaming panel before dropping the record,
        // otherwise the Xtream line is orphaned and keeps streaming.
        $provisioning->delete($subscription);

        $subscription->delete();

        return redirect()->route('admin.subscriptions.list')
            ->with('success', __tr('Subscription deleted successfully'));
    }

    public function sendPaymentLink(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'plan_id' => 'required|integer|exists:pricing_plans,id',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $plan = PricingPlan::findOrFail($request->plan_id);

        $link = PaymentLink::create([
            'user_id'    => $user->id,
            'plan_id'    => $plan->id,
            'expires_at' => now()->addDays(3),
        ]);

        $paymentUrl = route('payment.link', $link->token);

        \Illuminate\Support\Facades\Mail::to($user->email)
            ->queue(new \App\Mail\PaymentLinkMail($user, $paymentUrl, $plan));

        return back()->with('success', __tr('Payment link sent to ') . $user->email);
    }

    public function reprovision(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:user_subscriptions,id']);

        $subscription = UserSubscription::with('plan', 'user')->findOrFail($request->id);

        if (!get_setting('iptv_provisioning_enabled', 0)) {
            return back()->with('error', __tr('IPTV provisioning is disabled in settings.'));
        }

        dispatch(new ProvisionSubscriptionJob($subscription));

        return back()->with('success', __tr('Provisioning job dispatched for subscription #') . $subscription->id);
    }
}
