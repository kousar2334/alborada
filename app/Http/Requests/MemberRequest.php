<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
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
    public function rules(Request $request)
    {
        if ($request->has('id') && $request['id'] != null) {
            return [
                'name' => 'required|max:250',
                'phone' => 'required|phone:BD|max:250|unique:users,phone,' . $request->id,
                'email' => 'required|max:250|email|unique:App\Models\User,email,' . $request->id
            ];
        } else {
            return [
                'name' => 'required|max:250',
                'password' => 'required|confirmed|min:6|max:250',
                'phone' => 'required|phone:BD|max:250|unique:users,phone',
                'email' => 'required|max:250|email|unique:App\Models\User,email',
                // 'terms_conditions' => 'accepted'
            ];
        }
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
            'phone.required' => __tr('Phone is required'),
            'phone.phone' => __tr('Incorrect phone number'),
            'phone.unique' => __tr('Phone is already used'),
            'email.required' => __tr('Email is required'),
            'email.email' => __tr('Incorrect email'),
            'email.unique' => __tr('Email is already used'),
        ];
    }
}
