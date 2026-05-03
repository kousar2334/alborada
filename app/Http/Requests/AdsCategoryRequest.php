<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdsCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|max:250',
        ];
    }

    public function messages()
    {

        return [
            'title.required' => __tr('Title is required'),
            'parent.exists' => __tr('Selected parent does not exists'),

        ];
    }
}
