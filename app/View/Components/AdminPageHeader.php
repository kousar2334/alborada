<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdminPageHeader extends Component
{

    public $title;
    public $links;
    public $has_home_link;
    /**
     * Create a new component instance.
     */
    public function __construct($title, $links = [], $has_home_link = true)
    {
        $this->title = $title;
        $this->links = $links;
        $this->has_home_link = $has_home_link;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('backend.components.admin-page-header');
    }
}
