<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'first_name' => 'sometimes|required|string|max:255',
            'last_name'  => 'sometimes|required|string|max:255',
            'phone'      => 'sometimes|required|string|max:255',
            'dial_code'  => 'sometimes|required|string|max:255',
            'city'       => 'sometimes|required|string|max:255',
            'country'    => 'sometimes|required|string|max:255',
            'zip_code'   => 'sometimes|required|string|max:255',
            'email'      => 'sometimes|required|email|unique:users,email,' . $this->user->id,
            'password'   => 'nullable|string|min:8|confirmed',
        ];
    }
}
