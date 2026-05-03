<?php

namespace App\Http\ApiResource;;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\ApiResource;

\ChatMessageCollection;

class SingleChatResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'receiver_user_id' => $this->receiver_user_id,
            'ad_uid' => $this->ad != null ? $this->ad->uid : '',
            'ad_slug' => $this->ad != null ? Str::slug($this->ad->title) : '',
            'ad_title' => $this->ad != null ? $this->ad->translation('title', session()->get('api_locale')) : '',
            'ad_image' => $this->ad != null ? getFilePath($this->ad->thumbnail_image, true, '240x160') : '',
            'messages' => new ChatMessageCollection($this->messages)
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
