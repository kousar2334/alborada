<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\HomePageSection;
use App\Models\PricingPlan;
use App\Repository\PageRepository;

class PageController extends Controller
{
    public function __construct(
        public PageRepository $page_repository,
    ) {}

    public function homePage()
    {
        $activeStatus = config('settings.general_status.active');

        $pricingPlans = PricingPlan::with('pricing_plan_translations')
            ->where('status', $activeStatus)
            ->orderBy('price', 'ASC')
            ->get();

        $homeSections = HomePageSection::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('frontend.pages.home', compact(
            'pricingPlans',
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
