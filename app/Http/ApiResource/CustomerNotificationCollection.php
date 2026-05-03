<?php

namespace App\Http\ApiResource;;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerNotificationCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'message' => $this->getMessage($data),
                    'link' => $this->getLink($data),
                    'time' => $data->created_at->diffForHumans()
                ];
            })
        ];
    }

    public function getMessage($item)
    {
        $message = $item->data['message'];
        return $message;
    }

    public function getLink($item)
    {
        $link = $item->data['link'];
        return $link;
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
