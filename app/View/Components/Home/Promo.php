<?php

namespace App\View\Components\Home;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Promo extends Component
{
    public function render(): View|Closure|string
    {
        return view('frontend.components.home.promo');
    }
}
