<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adder extends Model
{
    protected $fillable = ['name', 'price','type','min_qty','max_qty'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_adder');
    }
}
