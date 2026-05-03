<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
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
            $rules['code']   = 'required|max:250';
            $rules['status'] = 'required|in:0,1';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __tr('Name is required'),
            'code.required' => __tr('Code is required'),
        ];
    }
}
