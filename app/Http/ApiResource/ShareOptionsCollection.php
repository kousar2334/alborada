<?php

namespace App\Http\ApiResource;;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShareOptionsCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'network' => $data['network'],
                    'network_name' => $data['network_name'],
                    'icon' => $data['icon']
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
        ];
    }

    public function toResponse($request)
    {
        return parent::toResponse($request)->setStatusCode(200);
    }
}
