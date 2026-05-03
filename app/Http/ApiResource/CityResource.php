<?php

namespace App\Http\ApiResource;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CityResource extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->translation('name', session()->get('name')),
                    'state_id' => $data->state_id
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
