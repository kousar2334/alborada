<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdReport;
use App\Models\AdsCategory;
use App\Models\Blog;
use App\Models\ContactUsMessage;
use App\Models\Page;
use App\Models\SavedAd;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Will redirect admin dashboard
     */
    public function adminDashboard(): View
    {
        $active   = config('settings.general_status.active');
        $inactive = config('settings.general_status.in_active');

        // --- Stat Boxes (Ad counts in one query) ---
        $ad_counts = Ad::selectRaw("
            COUNT(*) as total_ads,
            SUM(status = ?) as active_ads,
            SUM(status = ?) as pending_ads,
            SUM(is_featured = ?) as featured_ads
        ", [$active, $inactive, $active])->first();

        $total_ads    = $ad_counts->total_ads;
        $active_ads   = $ad_counts->active_ads;
        $pending_ads  = $ad_counts->pending_ads;
        $featured_ads = $ad_counts->featured_ads;
        $total_members    = User::count();
        $total_categories = AdsCategory::count();
        $total_blogs      = Blog::count();
        $total_page       = Page::count();
        $total_reports    = AdReport::count();
        $unread_messages  = ContactUsMessage::where('is_read', false)->count();
        $total_saved_ads  = SavedAd::count();
        $active_subs      = UserSubscription::where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();

        // --- Latest Ads ---
        $latest_ads = Ad::with(['categoryInfo', 'userInfo', 'cityInfo'])
            ->latest()
            ->take(10)
            ->get();

        // --- Latest Members ---
        $latest_members = User::latest()->take(10)->get();

        // --- Latest Reports ---
        $latest_reports = AdReport::with(['ad', 'user', 'reason'])
            ->latest()
            ->take(8)
            ->get();

        // --- Latest Unread Messages ---
        $latest_messages = ContactUsMessage::where('is_read', false)
            ->latest()
            ->take(8)
            ->get();

        // --- Monthly charts (last 12 months) ---
        $now         = now();
        $periodStart = $now->copy()->subMonths(11)->startOfMonth();

        $monthly_ads = Ad::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', $periodStart)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthly_members_raw = User::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', $periodStart)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthly_labels       = [];
        $monthly_data         = [];
        $monthly_members_data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date                   = $now->copy()->subMonths($i);
            $key                    = $date->format('Y-m');
            $monthly_labels[]       = $date->format('M Y');
            $monthly_data[]         = $monthly_ads[$key] ?? 0;
            $monthly_members_data[] = $monthly_members_raw[$key] ?? 0;
        }

        // --- Ads by Category (top 8) doughnut chart ---
        $ads_by_category = AdsCategory::withCount('ads')
            ->orderByDesc('ads_count')
            ->take(8)
            ->get();

        $category_labels = $ads_by_category->pluck('title')->toArray();
        $category_data   = $ads_by_category->pluck('ads_count')->toArray();

        return view('backend.modules.dashboard.index', compact(
            'total_ads',
            'active_ads',
            'pending_ads',
            'featured_ads',
            'total_members',
            'total_categories',
            'total_blogs',
            'total_page',
            'total_reports',
            'unread_messages',
            'total_saved_ads',
            'active_subs',
            'latest_ads',
            'latest_members',
            'latest_reports',
            'latest_messages',
            'monthly_labels',
            'monthly_data',
            'monthly_members_data',
            'category_labels',
            'category_data',
            'active'
        ));
    }
}
