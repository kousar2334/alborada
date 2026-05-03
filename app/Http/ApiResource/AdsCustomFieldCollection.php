<?php

namespace App\Http\ApiResource;;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\ApiResource;

\AdsCustomFieldOptionCollection;

class AdsCustomFieldCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'title' => $data->translation('title', session()->get('api_local')),
                    'is_required' => $data->is_required,
                    'value' => $data->type == config('settings.input_types.checkbox') ? [] : $data->default_value,
                    'type' => $data->type,
                    'options' => new AdsCustomFieldOptionCollection($data->options)
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
