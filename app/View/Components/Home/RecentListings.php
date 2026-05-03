<?php

namespace App\View\Components\Home;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RecentListings extends Component
{
    public function __construct(
        public $recentListings,
    ) {}

    public function render(): View|Closure|string
    {
        return view('frontend.components.home.recent-listings');
    }
}
