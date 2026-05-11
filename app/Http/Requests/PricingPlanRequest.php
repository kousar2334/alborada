<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PricingPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isTranslation = $this->input('lang') && $this->input('lang') !== defaultLangCode();

        return [
            'title'                      => 'required|string|max:250',
            'duration_days'              => $isTranslation ? 'sometimes' : 'required|integer|min:1',
            'price'                      => $isTranslation ? 'sometimes' : 'required|numeric|min:0',
            'listing_quantity'           => $isTranslation ? 'sometimes' : 'required|integer|min:0',
            'featured_listing_quantity'  => $isTranslation ? 'sometimes' : 'required|integer|min:0',
            'gallery_image_quantity'     => $isTranslation ? 'sometimes' : 'required|integer|min:0',
            'membership_badge'           => 'nullable|in:0,1',
            'status'                     => $isTranslation ? 'sometimes' : 'required|in:0,1',
            'max_connections'            => $isTranslation ? 'sometimes' : 'required|integer|min:1|max:99',
            'streaming_quality'          => $isTranslation ? 'sometimes' : 'required|in:SD,HD,FHD,4K',
            'catchup_days'               => $isTranslation ? 'sometimes' : 'required|integer|min:0',
            'dvr_enabled'                => 'nullable|in:0,1',
            'is_trial'                   => 'nullable|in:0,1',
            'trial_days'                 => 'nullable|integer|min:1',
            'sort_order'                 => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __tr('Plan title is required'),
            'duration_days.required' => __tr('Duration days is required'),
            'duration_days.min' => __tr('Duration must be at least 1 day'),
            'price.required' => __tr('Price is required'),
            'price.numeric' => __tr('Price must be a number'),
            'listing_quantity.required' => __tr('Listing quantity is required'),
            'featured_listing_quantity.required' => __tr('Featured listing quantity is required'),
            'gallery_image_quantity.required' => __tr('Gallery image quantity is required'),
        ];
    }
}
