<?php

namespace App\Http\ApiResource;;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\ApiResource;

\AdAdditionalInfoCollection;

class SingleAdResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uid' => $this->uid,
            'title' => $this->translation('title', session()->get('api_locale')),
            'post_date' => $this->created_at->diffForHumans(),
            'thumbnail' => getFilePath($this->thumbnail_image, true, '240x160'),
            'gallery_images' => sizeof($this->galleryImages) > 0 ? new AdGalleryImageCollection($this->galleryImages) : $this->generateGalleryImages(),
            'item_condition' => $this->condition != null ? $this->condition->translation('title', session()->get('api_locale')) : null,
            'description' => $this->translation('description', session()->get('api_locale')),
            'price' => $this->price,
            'is_negotiable' => $this->is_negotiable,
            'is_featured' => $this->is_featured,
            'category' => $this->categoryInfo != null ? $this->categoryInfo->translation('title', session()->get('api_locale')) : null,
            'location' => $this->cityInfo != null ? $this->getLocation() : null,
            'author' => $this->authorInfo(),
            'additional_info' => new AdAdditionalInfoCollection(collect($this->customFields())),
            'google_api_key' => getGeneralSetting('google_map_api_key'),
            'url' => url('/') . "/ads" . '/' . Str::slug($this->title) . '/' . $this->uid,
            'total_views' => $this->view_counter
        ];
    }

    public function generateGalleryImages()
    {
        $data[0]
            = [
                'image_id' => $this->thumbnail_image,
                'regular' => getFilePath($this->thumbnail_image, false, '240x160'),
                'zoom' => getFilePath($this->thumbnail_image, false, '825x550'),
                'type' => 'image'
            ];
        return $data;
    }

    public function getAdditionalInfo()
    {
        return $this->customFields();
    }

    public function getLocation()
    {
        return [
            'city' => $this->cityInfo->translation('name', session()->get('api_locale')),
            'state' => $this->cityInfo->state->translation('name', session()->get('api_locale')),
        ];
    }

    public function authorInfo()
    {
        return [
            'id' => $this->userInfo->id,
            'name' => $this->userInfo->name,
            'image' => getFilePath($this->userInfo->image, true),
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'contact_is_hide' => $this->contact_is_hide,
            'join_date' => $this->userInfo->created_at->diffForHumans()
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
