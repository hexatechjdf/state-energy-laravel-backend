<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class PasswordChangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password'      => 'required|string',
            'new_password'          => 'required|string|min:8|confirmed',
            'email'                 => 'sometimes|required|email|unique:users,email,' . $this->user()->id,
        ];
    }
    public function messages(): array
    {
        return [
            'current_password.required' => 'Your current password is required.',
            'new_password.required'     => 'A new password is required.',
            'new_password.min'          => 'The new password must be at least 8 characters.',
            'new_password.confirmed'    => 'The new password confirmation does not match.',
        ];
    }
}
