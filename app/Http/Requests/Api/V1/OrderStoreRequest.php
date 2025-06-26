<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
            'first_name'              => 'required|string',
            'last_name'               => 'required|string',
            'email'                   => 'required|email',
            'phone_number'            => 'required|string',
            'address'                 => 'required|string',
            'zip_code'                => 'required|string',
            'city'                    => 'required|string',
            'monthly_utility_bill'    => 'nullable|numeric',
            'monthly_insurance_bill'  => 'nullable|numeric',
            'loan_financed_amount'    => 'nullable|numeric',
            'finance_provider'        => 'nullable|string',
            'appointment_id'          => 'nullable|string',
            'contact_id'              => 'nullable|string',
        ];
    }
}
