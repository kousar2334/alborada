<?php

namespace App\View\Components;

use Closure;
use App\Models\MenuItem;
use App\Models\MenuPosition;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class SiteFooter extends Component
{
    public $footer_menu_items = [];

    public function __construct()
    {
        $menu_position = MenuPosition::where('position_id', config('settings.menu_position.footer'))
            ->select('menu_id')
            ->first();

        $menu_id = $menu_position?->menu_id;

        $this->footer_menu_items = MenuItem::with(['menu_translations', 'children' => function ($q) {
            $q->orderBy('position', 'ASC');
        }, 'linkable'])
            ->where('menu_id', $menu_id)
            ->where('parent', null)
            ->orderBy('position', 'ASC')
            ->get();
    }

    public function render(): View|Closure|string
    {
        return view('frontend/includes/footer');
    }
}
