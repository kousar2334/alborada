<?php

namespace App\Http\ApiResource;;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatCollection extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'uid' => $data->uid,
                    'ad' => $data->ad != null ? $data->ad->translation('title', session()->get('api_locale')) : '',
                    'ad_thumbnail' => $data->ad != null ? getFilePath($data->ad->thumbnail_image, true, '240x160') : '',
                    'last_message' => $data->lastMessage() != null ? $data->lastMessage()->message : '',
                    'sender_name' => $data->lastMessage() != null ? $data->lastMessage()->sender->name : '',
                    'sender_image' => $data->lastMessage() != null ? getFilePath($data->lastMessage()->sender->image, true) : '',
                    'time' => $data->lastMessage() != null ? $data->lastMessage()->created_at->diffForHumans() : '',
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
