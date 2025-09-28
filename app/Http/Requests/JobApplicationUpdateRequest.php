<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobApplicationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'status' => 'bail|required|string|in:pending,accepted,rejected',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'The application status is required.',
            'status.in' => 'The application must be either pending, accepted, rejected.',
        ];
    }
}
