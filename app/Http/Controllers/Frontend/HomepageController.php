<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\FeaturedContent;
use App\Models\HomePageSection;
use App\Models\MediaContent;
use App\Models\PricingPlan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomepageController extends Controller
{
    public function index(): View
    {
        $sections = $this->activeSections();

        $plans = PricingPlan::where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        $featuredContent = FeaturedContent::where('is_active', true)
            ->whereNotIn('type', ['sports_event'])
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $upcomingEvents = FeaturedContent::where('is_active', true)
            ->where('type', 'sports_event')
            ->where(function ($q) {
                $q->whereNull('event_date')->orWhere('event_date', '>=', now()->toDateString());
            })
            ->orderBy('event_date')
            ->take(6)
            ->get();

        $tvShows = MediaContent::where('is_active', true)
            ->where('featured_on_home', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->take(12)
            ->get();

        $channels = Channel::where('status', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('frontend.pages.home', compact('sections', 'plans', 'featuredContent', 'upcomingEvents', 'tvShows', 'channels'));
    }

    private function activeSections(): array
    {
        $defaults = [
            'hero'             => ['title' => 'Hero', 'sort_order' => 10],
            'about'            => ['title' => 'About', 'sort_order' => 20],
            'featured_content' => ['title' => 'Featured Content', 'sort_order' => 25],
            'categories'       => ['title' => 'Content Categories', 'sort_order' => 30],
            'features'         => ['title' => 'Features', 'sort_order' => 40],
            'pricing'          => ['title' => 'Pricing Plans', 'sort_order' => 50],
            'upcoming_events'  => ['title' => 'Upcoming Events', 'sort_order' => 55],
            'reviews'          => ['title' => 'Reviews', 'sort_order' => 60],
            'portal'           => ['title' => 'Client Portal Preview', 'sort_order' => 70],
            'devices'          => ['title' => 'Compatible Devices', 'sort_order' => 80],
            'setup'            => ['title' => 'How to Order', 'sort_order' => 90],
            'why'              => ['title' => 'Why Choose Us', 'sort_order' => 100],
            'tv_shows'         => ['title' => 'Movies & TV Shows', 'sort_order' => 105],
            'channels'         => ['title' => 'Channel Lineup', 'sort_order' => 110],
            'faq'              => ['title' => 'FAQ', 'sort_order' => 120],
            'reseller'         => ['title' => 'Reseller Program', 'sort_order' => 130],
            'cta'              => ['title' => 'CTA Banner', 'sort_order' => 140],
            'newsletter'       => ['title' => 'Newsletter', 'sort_order' => 150],
        ];

        if (!Schema::hasTable('home_page_sections')) {
            return collect($defaults)->mapWithKeys(fn($v, $k) => [
                $k => (object) ['key' => $k, 'title' => $v['title'], 'is_active' => true, 'sort_order' => $v['sort_order']],
            ])->all();
        }

        foreach ($defaults as $key => $data) {
            HomePageSection::firstOrCreate(
                ['key' => $key],
                ['title' => $data['title'], 'sort_order' => $data['sort_order'], 'is_active' => true]
            );
        }

        return HomePageSection::orderBy('sort_order')
            ->get()
            ->keyBy('key')
            ->all();
    }
}
