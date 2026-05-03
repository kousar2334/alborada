<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAdUpdateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|max:250',
            'price' => 'required|max:200',
            'description' => 'required',
            'category' => 'required|max:10',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __tr('Title is required', session()->get('api_local')),
            'price.required' => __tr('Price is required', session()->get('api_local')),
            'description.required' => __tr('Description is required', session()->get('api_local')),
            'city.required' => __tr('Please select a city', session()->get('api_local')),
            'category.required' => __tr('Please select a category', session()->get('api_local')),
            'thumbnail_image.required' => __tr('Thumbnail image is required', session()->get('api_local')),
            'contact_email.required' => __tr('Email is required', session()->get('api_local')),
            'contact_phone.required' => __tr('Phone is required', session()->get('api_local')),
        ];
    }
}
