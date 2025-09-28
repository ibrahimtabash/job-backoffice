<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'password' => 'bail|required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'The user password is required.',
            'password.min' => 'The user password must be at least 8 characters.',
        ];
    }
}
