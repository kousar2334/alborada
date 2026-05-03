<?php

namespace App\Http\ApiResource;;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AdCategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => (int) $data->id,
                    'name' => $data->translation('title', session()->get('api_locale')),
                    'permalink' => $data->permalink,
                    'parent' => $data->parent,
                    'image' => getFilePath($data->image, false),
                    'icon' => getFilePath($data->icon, false),
                    'total_child' => $data->child->count(),
                ];
            })
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
