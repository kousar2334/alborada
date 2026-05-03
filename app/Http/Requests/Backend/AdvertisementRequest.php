<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class AdvertisementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'      => 'required|max:255',
            'position'   => 'required|in:home_top,listing_top,details_sidebar,details_top',
            'type'       => 'required|in:image,html',
            'image_path'      => 'nullable|string',
            'edit_image_path' => 'nullable|string',
            'click_url'  => 'nullable|url|max:500',
            'html_code'  => 'required_if:type,html',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'sort_order' => 'nullable|integer|min:0',
            'status'     => 'required|in:1,2',
        ];
    }
}
