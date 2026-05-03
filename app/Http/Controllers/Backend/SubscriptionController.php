<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionApproved;
use App\Notifications\SubscriptionRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = UserSubscription::with(['user', 'plan'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('q')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        $subscriptions = $query->paginate(20)->withQueryString();

        $stats = [
            'total'        => UserSubscription::count(),
            'active'       => UserSubscription::where('status', 'active')->where('expires_at', '>', now())->count(),
            'pending'      => UserSubscription::where('status', 'pending')->count(),
            'expired'      => UserSubscription::where('status', 'active')->where('expires_at', '<=', now())->count(),
            'bank_pending' => UserSubscription::where('status', 'pending')->where('payment_method', 'bank_transfer')->count(),
        ];

        return view('backend.modules.subscriptions.index', compact('subscriptions', 'stats'));
    }

    public function approve(Request $request)
    {
        $subscription = UserSubscription::with('user', 'plan')
            ->where('payment_method', 'bank_transfer')
            ->where('status', 'pending')
            ->findOrFail($request->id);

        try {
            DB::beginTransaction();

            $subscription->update([
                'status'     => 'active',
                'starts_at'  => now(),
                'expires_at' => now()->addDays($subscription->plan->duration_days),
                'admin_note' => $request->admin_note ?? null,
            ]);

            // Notify user
            $subscription->user->notify(new SubscriptionApproved($subscription));

            DB::commit();

            toastNotification('success', 'Subscription approved and user notified.', 'Approved');
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Failed to approve subscription.', 'Error');
        }

        return redirect()->back();
    }

    public function reject(Request $request)
    {
        $subscription = UserSubscription::with('user', 'plan')
            ->where('payment_method', 'bank_transfer')
            ->where('status', 'pending')
            ->findOrFail($request->id);

        try {
            DB::beginTransaction();

            $subscription->update([
                'status'     => 'rejected',
                'admin_note' => $request->admin_note ?? null,
            ]);

            // Notify user
            $subscription->user->notify(new SubscriptionRejected($subscription));

            DB::commit();

            toastNotification('success', 'Subscription rejected and user notified.', 'Rejected');
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Failed to reject subscription.', 'Error');
        }

        return redirect()->back();
    }

    public function delete(Request $request)
    {
        $subscription = UserSubscription::findOrFail($request->id);
        $subscription->delete();

        toastNotification('success', 'Subscription deleted successfully', 'Success');
        return redirect()->back();
    }
}
