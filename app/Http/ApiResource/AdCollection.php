<?php

namespace App\Http\ApiResource;;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AdCollection extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'uid' => $data->uid,
                    'slug' => Str::slug($data->title),
                    'title' => $data->translation('title', session()->get('api_locale')),
                    'thumbnail' => getFilePath($data->thumbnail_image, true, '240x160'),
                    'price' => $data->price,
                    'cost' => $data->cost,
                    'is_negotiable' => $data->is_negotiable,
                    'is_featured' => $data->is_featured,
                    'status' => $data->status,
                    'payable' => $data->isPaymentPending(),
                    'city_id' => $data->city,
                    'city' => $data->cityInfo != null ? $data->cityInfo->translation('name', session()->get('api_locale')) : null,
                    'category_id' => $data->category,
                    'category' => $data->categoryInfo != null ? $data->categoryInfo->translation('title', session()->get('api_locale')) : null,
                    'category_permalink' => $data->categoryInfo != null ? $data->categoryInfo->permalink : null,
                    'post_date' => $data->created_at->diffForHumans()
                ];
            }),
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
