<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinancingAmountRequest extends FormRequest
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
    public function rules()
    {
        return [
            'mosaic_apr'       => ['nullable', 'required_without_all:renew_solar_apr'],
            'renew_solar_apr'  => ['nullable', 'required_without_all:mosaic_apr'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
        ];
    }

    public function messages()
    {
        return [
            'mosaic_apr.required_without_all'      => 'At least one APR value (Mosaic or Renew Solar) is required.',
            'renew_solar_apr.required_without_all' => 'At least one APR value (Mosaic or Renew Solar) is required.',
            'mosaic_apr.numeric'                   => 'Mosaic APR must be a valid number.',
            'mosaic_apr.between'                   => 'Mosaic APR must be between 0 and 100.',
            'renew_solar_apr.numeric'              => 'Renew Solar APR must be a valid number.',
            'renew_solar_apr.between'              => 'Renew Solar APR must be between 0 and 100.',
        ];
    }
}
