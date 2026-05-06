<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
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
}
