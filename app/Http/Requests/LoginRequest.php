<?php

namespace App\Http\Requests;

class LoginRequest extends BaseApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ];
    }
}
