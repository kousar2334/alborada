<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdsCategory;
use App\Models\HomePageSection;
use App\Models\PricingPlan;
use App\Repository\AdvertisementRepository;
use App\Repository\PageRepository;

class PageController extends Controller
{
    public function __construct(
        public AdvertisementRepository $advertisement_repository,
        public PageRepository $page_repository,
    ) {}

    public function homePage()
    {
        $activeStatus = config('settings.general_status.active');

        // Fetch active parent categories sorted by number of ads (most ads first)
        $categories = AdsCategory::with(['ads_category_translations'])->whereNull('parent')
            ->where('status', $activeStatus)
            ->withCount(['ads' => function ($q) use ($activeStatus) {
                $q->where('status', $activeStatus);
            }])
            ->orderByDesc('ads_count')
            ->get();

        // Fetch featured/top listings
        $topListings = Ad::where('status', $activeStatus)
            ->where('is_featured', $activeStatus)
            ->with(['cityInfo.city_translations', 'stateInfo.state_translations'])
            ->latest()
            ->take(8)
            ->get();

        // Featured ads section (same data, independent section)
        $featuredAds = $topListings;

        // Fetch recent listings
        $recentListings = Ad::where('status', $activeStatus)
            ->with(['cityInfo.city_translations', 'stateInfo.state_translations'])
            ->latest()
            ->take(8)
            ->get();

        // Fetch category-wise listings for top 3 parent categories
        $topCategories = AdsCategory::with(['ads_category_translations'])->whereNull('parent')
            ->where('status', $activeStatus)
            ->orderBy('id', 'ASC')
            ->take(3)
            ->get();

        // Get all child category IDs in one query
        $allChildCategories = AdsCategory::whereIn('parent', $topCategories->pluck('id'))
            ->where('status', $activeStatus)
            ->get(['id', 'parent']);

        // Build category ID map: parent_id => [parent_id, child_id1, child_id2, ...]
        $categoryIdMap = [];
        foreach ($topCategories as $category) {
            $childIds = $allChildCategories->where('parent', $category->id)->pluck('id')->toArray();
            $categoryIdMap[$category->id] = array_merge([$category->id], $childIds);
        }

        // Fetch all ads for all categories in one query
        $allCategoryIds = collect($categoryIdMap)->flatten()->unique()->toArray();
        $allAds = Ad::where('status', $activeStatus)
            ->whereIn('category_id', $allCategoryIds)
            ->with(['cityInfo.city_translations', 'stateInfo.state_translations'])
            ->latest()
            ->get();

        // Group ads by parent category
        $categoryWiseListings = [];
        foreach ($topCategories as $category) {
            $categoryIds = $categoryIdMap[$category->id];
            $ads = $allAds->whereIn('category_id', $categoryIds)->take(8)->values();

            if ($ads->count() > 0) {
                $categoryWiseListings[] = [
                    'category' => $category,
                    'ads' => $ads,
                ];
            }
        }

        // Fetch active pricing plans
        $pricingPlans = PricingPlan::with('pricing_plan_translations')
            ->where('status', $activeStatus)
            ->orderBy('price', 'ASC')
            ->get();

        // Total active ads count for banner stats
        $totalAdsCount = Ad::where('status', $activeStatus)->count();

        // Advertisements for homepage
        $advertisements = $this->advertisement_repository->getActiveByPosition('home_top');

        // Home page section order and visibility
        $homeSections = HomePageSection::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('frontend.pages.home', compact(
            'categories',
            'topListings',
            'featuredAds',
            'recentListings',
            'categoryWiseListings',
            'pricingPlans',
            'totalAdsCount',
            'advertisements',
            'homeSections'
        ));
    }

    /**
     * Display a single static page by permalink
     */
    public function pagePreview(string $permalink)
    {
        $page = $this->page_repository->getPageByPermalink($permalink);

        abort_if($page === null, 404);

        return view('frontend.pages.page-single', compact('page'));
    }

    /**
     * Display the pricing plans page
     */
    public function pricingPlans()
    {
        $activeStatus = config('settings.general_status.active');

        $pricingPlans = PricingPlan::with('pricing_plan_translations')
            ->where('status', $activeStatus)
            ->orderBy('price', 'ASC')
            ->get();

        return view('frontend.pages.pricing-plans', compact('pricingPlans'));
    }
}
