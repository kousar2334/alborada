<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SafetyTipsRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|max:250'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __tr('Title is required')
        ];
    }
}
