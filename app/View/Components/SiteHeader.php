<?php

namespace App\View\Components;

use Closure;
use App\Models\Language;
use App\Models\MenuItem;
use App\Models\MenuPosition;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class SiteHeader extends Component
{
    public $menu_items = [];
    public $languages = [];
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $menu_position = MenuPosition::where('position_id', config('settings.menu_position.header'))
            ->select('menu_id')
            ->first();
        $menu_id = $menu_position != null ? $menu_position->menu_id : null;
        $this->menu_items = MenuItem::with(['menu_translations', 'children' => function ($q) {
            $q->orderBy('position', 'ASC');
        }, 'linkable'])
            ->where('menu_id', $menu_id)
            ->where('parent', NULL)
            ->orderBy('position', 'ASC')
            ->get();

        $this->languages = Language::where('status', config('settings.general_status.active'))->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('frontend/includes/header');
    }
}
