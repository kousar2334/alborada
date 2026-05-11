<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\ProvisionSubscriptionJob;
use App\Models\PaymentLink;
use App\Models\PricingPlan;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionApproved;
use App\Notifications\SubscriptionRejected;
use Illuminate\Http\Request;

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
            $query->where('payment_method', $request->method);
        }

        $subscriptions = $query->paginate(20)->withQueryString();

        $stats = [
            'total'        => UserSubscription::count(),
            'active'       => UserSubscription::where('status', 'active')->count(),
            'pending'      => UserSubscription::where('status', 'pending')->count(),
            'bank_pending' => UserSubscription::where('status', 'pending')->where('payment_method', 'bank_transfer')->count(),
            'expired'      => UserSubscription::where('status', 'active')->where('expires_at', '<', now())->count(),
        ];

        return view('backend.modules.subscriptions.index', compact('subscriptions', 'stats'));
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

    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:user_subscriptions,id']);

        UserSubscription::findOrFail($request->id)->delete();

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
}
