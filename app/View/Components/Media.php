<?php

namespace App\View\Components;

use Closure;
use App\Models\Media as MediaModel;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Media extends Component
{

    public $multiple = false;
    public $name;
    public $value;
    public $media_ids;
    public $width;
    /**
     * Create a new component instance.
     */
    public function __construct($name, $value = null, $width = false, $multiple = false)
    {
        $this->multiple = $multiple;
        $this->name = $name;
        $this->value = $value;
        $this->width = $width;

        if ($multiple) {
            $paths = $value ? array_filter(array_map('trim', explode(',', $value))) : [];
            $this->media_ids = MediaModel::whereIn('path', $paths)->pluck('id');
        }

        if (!$multiple) {
            $this->media_ids = MediaModel::where('path', $value)->take(1)->pluck('id');
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('backend.media.media');
    }
}
