<?php

namespace App\Http\ApiResource;;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AdsCustomFieldOptionCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($data) {
            return [
                'id' => $data->option_id != null ? $data->option_id : $data->id,
                'field_id' => $data->field_id,
                'value' => $data->translation('value', session()->get('api_locale'))
            ];
        });
    }
}
