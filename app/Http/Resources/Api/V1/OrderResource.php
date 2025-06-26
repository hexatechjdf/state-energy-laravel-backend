<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {

        return [
            'id'                    => $this->id,
            'user_id'               => $this->user_id,
            'account_user'          => new UserResource($this->user),
            'first_name'            => $this->first_name,
            'last_name'             => $this->last_name,
            'phone_number'          => $this->phone_number,
            'address'               => $this->address,
            'zip_code'              => $this->zip_code,
            'city'                  => $this->city,
            'total_amount'          => $this->total_amount,
            'loan_financed_amount'  => $this->loan_financed_amount,
            'order_amount'          => $this->order_amount,
            'monthly_utility_bill'  => $this->monthly_utility_bill,
            'monthly_insurance_bill'=> $this->monthly_insurance_bill,
            'finance_provider'      => $this->finance_provider,
            "appointment_id"        => $this->appointment_id,
            "contact_id"            => $this->contact_id,
            'status'                => $this->status,
            'order_items'           => OrderItemResource::collection($this->orderItems),


        ];
    }
}
