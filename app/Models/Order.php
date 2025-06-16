<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'zip_code',
        'city',
        'monthly_utility_bill',
        'monthly_insurance_bill',
        'loan_financed_amount',
        'finance_provider',
        'total_amount',
        'status'
    ];
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
