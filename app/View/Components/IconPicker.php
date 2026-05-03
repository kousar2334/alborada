<?php

namespace App\View\Components;

use Closure;
use File;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class IconPicker extends Component
{
    public $icons = [];
    public $name = "";
    public $value = "";
    /**
     * Create a new component instance.
     */
    public function __construct($name, $value = null)
    {
        $jsonFilePath = public_path('web-assets/backend/js/icons.json');
        $jsonData = File::get($jsonFilePath);
        $this->icons = json_decode($jsonData, true);

        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('backend.components.icon-picker');
    }
}
