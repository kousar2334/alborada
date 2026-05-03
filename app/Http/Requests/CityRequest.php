<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $lang = $this->input('lang', defaultLangCode());
        $isDefaultLang = $lang == defaultLangCode();

        $rules = [
            'name' => 'required|max:250',
            'lang' => 'nullable|string|max:10',
        ];

        if ($isDefaultLang) {
            $rules['state_id'] = 'required|exists:states,id';
            $rules['status']   = 'required|in:0,1';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required'     => __tr('Name is required'),
            'state_id.required' => __tr('Please select a state'),
            'state_id.exists'   => __tr('Invalid state'),
        ];
    }
}
