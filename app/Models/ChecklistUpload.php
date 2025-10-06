<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistUpload extends Model
{
    protected $fillable = [
        'appointment_id',
        'order_id',
        'category',
        'field_name',
        'file_name',
        'crm_file_id',
        'crm_response',
    ];

    protected $casts = [
        'crm_response' => 'array',
    ];
}
