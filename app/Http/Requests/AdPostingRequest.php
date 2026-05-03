<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdPostingRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:250',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|min:50',
            'country' => 'required|integer|exists:countries,id',
            'state' => 'required|integer|exists:states,id',
            'city' => 'required|integer|exists:cities,id',
            'category' => 'required|integer|exists:ads_categories,id',
            'condition' => 'nullable|integer|exists:ads_conditions,id',
            'old_thumbnail_id' => 'nullable',
            'thumbnail_image' => 'required_if:old_thumbnail_id,null|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'contact_email' => 'required|email|max:200',
            'phone' => 'required|string|max:100',
            'address' => 'nullable|string|max:500',
            'video_url' => 'nullable|url|max:500',
            'negotiable' => 'nullable|boolean',
            'hide_phone_number' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string|max:100',
            'terms_conditions' => 'required|accepted',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __tr('Item name is required', session()->get('api_local')),
            'title.max' => __tr('Item name cannot exceed 250 characters', session()->get('api_local')),
            'price.required' => __tr('Price is required', session()->get('api_local')),
            'price.numeric' => __tr('Price must be a valid number', session()->get('api_local')),
            'price.min' => __tr('Price cannot be negative', session()->get('api_local')),
            'description.required' => __tr('Description is required', session()->get('api_local')),
            'description.min' => __tr('Description must be at least 50 characters', session()->get('api_local')),
            'country.required' => __tr('Please select a country', session()->get('api_local')),
            'country.exists' => __tr('Selected country is invalid', session()->get('api_local')),
            'state.required' => __tr('Please select a state', session()->get('api_local')),
            'state.exists' => __tr('Selected state is invalid', session()->get('api_local')),
            'city.required' => __tr('Please select a city', session()->get('api_local')),
            'city.exists' => __tr('Selected city is invalid', session()->get('api_local')),
            'category.required' => __tr('Please select a category', session()->get('api_local')),
            'category.exists' => __tr('Selected category is invalid', session()->get('api_local')),
            'thumbnail_image.required_if' => __tr('Featured image is required', session()->get('api_local')),
            'thumbnail_image.image' => __tr('Featured image must be a valid image file', session()->get('api_local')),
            'thumbnail_image.mimes' => __tr('Featured image must be jpg, jpeg, png, gif or webp', session()->get('api_local')),
            'thumbnail_image.max' => __tr('Featured image size cannot exceed 5MB', session()->get('api_local')),
            'contact_email.required' => __tr('Contact email is required', session()->get('api_local')),
            'contact_email.email' => __tr('Please provide a valid email address', session()->get('api_local')),
            'phone.required' => __tr('Phone number is required', session()->get('api_local')),
            'terms_conditions.required' => __tr('You must agree to the terms and conditions', session()->get('api_local')),
            'terms_conditions.accepted' => __tr('You must agree to the terms and conditions', session()->get('api_local')),
        ];
    }
}
