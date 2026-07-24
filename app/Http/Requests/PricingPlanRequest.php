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
            'offer_price'                => 'nullable|numeric|min:0|lt:price',
            'status'                     => $isTranslation ? 'sometimes' : 'required|in:0,1',
            'max_connections'            => $isTranslation ? 'sometimes' : 'required|integer|min:1|max:99',
            'streaming_quality'          => $isTranslation ? 'sometimes' : 'required|in:SD,HD,FHD,4K',
            'catchup_days'               => $isTranslation ? 'sometimes' : 'required|integer|min:0',
            'dvr_enabled'                => 'nullable|in:0,1',
            'is_trial'                   => 'nullable|in:0,1',
            'trial_days'                 => 'nullable|integer|min:1',
            'sort_order'                 => 'nullable|integer|min:0',
            'iptv_package_id'            => 'nullable|string|max:191',
            'iptv_sub_months'            => 'nullable|in:1,3,6,12',
            'iptv_device_type'           => 'nullable|in:m3u,mag',
            'iptv_country'               => 'nullable|string|max:10',
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
            'offer_price.lt' => __tr('Offer price must be lower than the regular price'),
        ];
    }
}
