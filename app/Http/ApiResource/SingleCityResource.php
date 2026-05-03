<?php

namespace App\Http\ApiResource;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleCityResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->translation('name', session()->get('api_locale')) . ', ' . $this->state->translation('name', session()->get('api_locale'))
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
        ];
    }
}
