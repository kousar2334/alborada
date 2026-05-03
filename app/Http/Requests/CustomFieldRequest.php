<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomFieldRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|max:250',
            'type' => 'required|max:2',
            'status' => 'nullable',
            'default_value' => 'nullable|max:250',
        ];
    }
}
