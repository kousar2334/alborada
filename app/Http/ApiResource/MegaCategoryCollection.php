<?php

namespace App\Http\ApiResource;;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MegaCategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return  $this->collection->map(function ($data) {
            return [
                'id' => (int) $data->id,
                'name' => $data->translation('title', session()->get('api_locale')),
                'permalink' => $data->permalink,
                'children' => new MegaCategoryCollection($data->child),
            ];
        });
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
