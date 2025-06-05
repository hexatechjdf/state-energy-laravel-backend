<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'user_config',
        'selected_adders',
        'calculated_total',
    ];

    protected $casts = [
        'user_config' => 'array',
        'selected_adders' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
