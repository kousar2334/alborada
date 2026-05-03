<?php

namespace App\View\Components\Home;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PricingPlans extends Component
{
    public function __construct(
        public $pricingPlans,
    ) {}

    public function render(): View|Closure|string
    {
        return view('frontend.components.home.pricing-plans');
    }
}
