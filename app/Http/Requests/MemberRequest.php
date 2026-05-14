<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if ($this->has('id') && $this->input('id') != null) {
            return [
                'name'  => 'required|max:250',
                'email' => 'required|max:250|email|unique:App\Models\User,email,' . $this->input('id'),
            ];
        }

        return [
            'name'     => 'required|max:250',
            'email'    => 'required|max:250|email|unique:App\Models\User,email',
            'password' => 'required|confirmed|min:6|max:250',
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => __tr('Name is required'),
            'password.required' => __tr('Password is required'),
            'password.confirmed' => __tr('Password does not match'),
            'password.min' => __tr('Password is too short'),

            'email.required' => __tr('Email is required'),
            'email.email'    => __tr('Incorrect email'),
            'email.unique'   => __tr('Email is already used'),
        ];
    }
}
