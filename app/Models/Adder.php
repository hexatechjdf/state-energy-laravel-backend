<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adder extends Model
{
    protected $fillable = ['name', 'price'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_adder');
    }
}
