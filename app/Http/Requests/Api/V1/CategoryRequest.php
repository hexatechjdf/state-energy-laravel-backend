<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Handle authorization later as needed
    }

    public function rules()
    {
        return [
            'name'          => 'required|string|max:255',
            'thumbnail'     => 'nullable|string|max:255',
            'detail_photo'  => 'nullable|string|max:255',
            'pricing'       => 'nullable|array',
            'configuration' => 'nullable|array',
            'adders'        => 'nullable|array',
            'adders.*'      => 'integer|exists:adders,id',
        ];
    }
}
