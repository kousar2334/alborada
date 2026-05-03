<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Media;
use App\Models\Menu;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Will redirect admin dashboard
     */
    public function adminDashboard(): View
    {
        $total_members    = User::count();
        $total_categories = BlogCategory::count();
        $total_blogs      = Blog::count();
        $total_pages      = Page::count();
        $total_media      = Media::count();
        $total_menus      = Menu::count();

        $latest_members = User::latest()->take(10)->get();

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

        $monthly_blogs_raw = Blog::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', $periodStart)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthly_labels        = [];
        $monthly_members_data  = [];
        $monthly_blogs_data    = [];

        for ($i = 11; $i >= 0; $i--) {
            $date                   = $now->copy()->subMonths($i);
            $key                    = $date->format('Y-m');
            $monthly_labels[]       = $date->format('M Y');
            $monthly_members_data[] = $monthly_members_raw[$key] ?? 0;
            $monthly_blogs_data[]   = $monthly_blogs_raw[$key] ?? 0;
        }

        $categories = BlogCategory::withCount('blogs')
            ->orderByDesc('blogs_count')
            ->take(8)
            ->get();

        $category_labels = $categories->pluck('title')->toArray();
        $category_data   = $categories->pluck('blogs_count')->toArray();

        return view('backend.modules.dashboard.index', compact(
            'total_members',
            'total_categories',
            'total_blogs',
            'total_pages',
            'total_media',
            'total_menus',
            'latest_members',
            'monthly_labels',
            'monthly_members_data',
            'monthly_blogs_data',
            'category_labels',
            'category_data'
        ));
    }
}
