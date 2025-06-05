<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'thumbnail',
        'detail_photo',
        'pricing',
        'configuration',
    ];

    protected $casts = [
        'pricing' => 'array',
        'configuration' => 'array',
    ];
    public function adders()
    {
        return $this->belongsToMany(Adder::class, 'category_adder');
    }
}
