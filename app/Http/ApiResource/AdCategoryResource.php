<?php

namespace App\Http\ApiResource;;

use Illuminate\Http\Resources\Json\JsonResource;

class AdCategoryResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->translation('title', session()->get('api_locale')),
            'permalink' => $this->permalink,
            'parent' => $this->parent,
            'image' => getFilePath($this->image, false),
            'icon' => getFilePath($this->icon, false),
            'total_child' => $this->child->count(),
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
