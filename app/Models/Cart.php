<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'configuration',
        'adders',
        'price',
        'configuration_meta',
        'pricing_meta',
        'appointment_id'
    ];

    protected $casts = [
        'configuration' => 'array',
        'adders'        => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

?>