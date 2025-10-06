<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Lender extends Model
{
    protected $appends = ['logo_url'];
    protected $fillable = [
        'url',
        'logo',
    ];

    /**
     * Get the full URL for the thumbnail photo.
     *
     * @return string|null
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }
        return Storage::disk('public')->url($this->logo);
    }
}
