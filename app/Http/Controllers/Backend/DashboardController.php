<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Invoice;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function adminDashboard(): View
    {
        $total_members = User::where('type', '!=', 0)->count();

        $active_subscriptions = UserSubscription::where('status', 'active')->count();

        $expiring_soon = UserSubscription::where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->count();

        $monthly_revenue = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_amount');

        $total_revenue = Invoice::where('status', 'paid')->sum('total_amount');

        $new_members_today = User::whereDate('created_at', today())->count();

        $pending_subscriptions = UserSubscription::where('status', 'pending')->count();

        $pending_tickets = SupportTicket::whereIn('status', [
            SupportTicket::STATUS_NEW,
            SupportTicket::STATUS_IN_PROGRESS,
        ])->count();

        $buffering_tickets = SupportTicket::where('department', 'buffering')
            ->whereNotIn('status', [SupportTicket::STATUS_CLOSED])
            ->count();

        $total_resellers = User::where('type', 1)->count();

        $latest_members = User::where('type', '!=', 0)->latest()->take(8)->get();

        $recent_subscriptions = UserSubscription::with(['user', 'plan'])
            ->latest()
            ->take(8)
            ->get();

        $now         = now();
        $periodStart = $now->copy()->subMonths(11)->startOfMonth();

        $monthly_members_raw = User::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', $periodStart)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthly_revenue_raw = Invoice::select(
            DB::raw("DATE_FORMAT(paid_at, '%Y-%m') as month"),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('status', 'paid')
            ->where('paid_at', '>=', $periodStart)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthly_labels       = [];
        $monthly_members_data = [];
        $monthly_revenue_data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date                   = $now->copy()->subMonths($i);
            $key                    = $date->format('Y-m');
            $monthly_labels[]       = $date->format('M Y');
            $monthly_members_data[] = $monthly_members_raw[$key] ?? 0;
            $monthly_revenue_data[] = $monthly_revenue_raw[$key] ?? 0;
        }

        return view('backend.modules.dashboard.index', compact(
            'total_members',
            'active_subscriptions',
            'expiring_soon',
            'monthly_revenue',
            'total_revenue',
            'new_members_today',
            'pending_subscriptions',
            'pending_tickets',
            'buffering_tickets',
            'total_resellers',
            'latest_members',
            'recent_subscriptions',
            'monthly_labels',
            'monthly_members_data',
            'monthly_revenue_data'
        ));
    }
}
