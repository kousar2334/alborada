<?php

namespace App\View\Components\Home;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Banner extends Component
{
    public function __construct(
        public $categories,
        public $totalAdsCount,
    ) {}

    public function render(): View|Closure|string
    {
        return view('frontend.components.home.banner');
    }
}
