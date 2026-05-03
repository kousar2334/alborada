<?php

namespace App\View\Components\Home;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CategoryListing extends Component
{
    public function __construct(
        public $catListing,
    ) {}

    public function render(): View|Closure|string
    {
        return view('frontend.components.home.category-listing');
    }
}
