<?php

namespace App\Http\ApiResource;;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatMessageCollection extends ResourceCollection
{

    public function toArray($request)
    {
        return $this->collection->map(function ($data) {
            return [
                'id' => $data->id,
                'message' => $data->message,
                'time' => $data->created_at->format('H:i A, d M Y'),
                'sender_id' => $data->sender != null ? $data->sender->id : null,
                'sender_name' => $data->sender != null ? $data->sender->name : null,
                'sender_image' => $data->sender != null ? getFilePath($data->sender->image, true) : null
            ];
        });
    }
}
