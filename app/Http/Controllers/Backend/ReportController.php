<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $totalRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$from, $to . ' 23:59:59'])
            ->sum('total_amount');

        $activeSubscriptions = UserSubscription::where('status', 'active')->count();
        $expiringSoon        = UserSubscription::where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->count();
        $pendingTickets      = \App\Models\SupportTicket::whereIn('status', [1, 2])->count();

        $topPlans = UserSubscription::select('plan_id', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
            ->groupBy('plan_id')
            ->with('plan:id,title')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('backend.modules.reports.index', compact(
            'from', 'to',
            'totalRevenue', 'activeSubscriptions', 'expiringSoon', 'pendingTickets',
            'topPlans'
        ));
    }

    public function revenueChart(Request $request): JsonResponse
    {
        $months = 12;
        $labels = [];
        $data   = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date     = now()->subMonths($i);
            $labels[] = $date->format('M Y');
            $key      = $date->format('Y-m');

            $data[$key] = Invoice::where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('total_amount');
        }

        return response()->json(['labels' => $labels, 'data' => array_values($data)]);
    }

    public function activeSubscribersChart(Request $request): JsonResponse
    {
        $months  = 12;
        $labels  = [];
        $active  = [];
        $expired = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date     = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $active[] = UserSubscription::where('status', 'active')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $expired[] = UserSubscription::where('status', 'expired')
                ->whereYear('expires_at', $date->year)
                ->whereMonth('expires_at', $date->month)
                ->count();
        }

        return response()->json(['labels' => $labels, 'active' => $active, 'expired' => $expired]);
    }

    public function expiringSoon(Request $request): View
    {
        $days = (int) $request->input('days', 7);

        $subscriptions = UserSubscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays($days)])
            ->orderBy('expires_at')
            ->paginate(30);

        return view('backend.modules.reports.expiring-soon', compact('subscriptions', 'days'));
    }

    public function resellerPerformance(Request $request): View
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $resellers = User::role('Reseller')
            ->withCount(['resellerClients as clients_count'])
            ->with([
                'creditLogs' => fn($q) => $q->whereBetween('created_at', [$from, $to . ' 23:59:59']),
            ])
            ->orderByDesc('clients_count')
            ->paginate(20);

        return view('backend.modules.reports.reseller-performance', compact('resellers', 'from', 'to'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $invoices = Invoice::with(['user', 'subscription.plan'])
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$from, $to . ' 23:59:59'])
            ->get();

        $filename = 'revenue-' . $from . '-to-' . $to . '.csv';

        return response()->streamDownload(function () use ($invoices) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice #', 'Customer', 'Email', 'Plan', 'Amount', 'Tax', 'Total', 'Paid At']);
            foreach ($invoices as $inv) {
                fputcsv($handle, [
                    $inv->invoice_number,
                    $inv->user->name ?? '',
                    $inv->user->email ?? '',
                    $inv->subscription->plan->title ?? 'N/A',
                    $inv->amount,
                    $inv->tax_amount,
                    $inv->total_amount,
                    $inv->paid_at?->format('Y-m-d H:i'),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
