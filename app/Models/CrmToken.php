<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CrmToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'company_id',
        'access_token',
        'refresh_token',
        'expires_in',
        'scope',
        'user_type',
    ];

    protected $casts = [
        'expires_in' => 'integer',
    ];
}
