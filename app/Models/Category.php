<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $appends = ['detail_photo_url', 'thumbnail_url'];
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
    /**
     * Get the full URL for the detail photo.
     *
     * @return string|null
     */
    public function getDetailPhotoUrlAttribute()
    {
        if (!$this->detail_photo) {
            return null;
        }

        return Storage::disk('public')->url($this->detail_photo);
    }

    /**
     * Get the full URL for the thumbnail photo.
     *
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail) {
            return null;
        }

        return Storage::disk('public')->url($this->thumbnail);
    }
}
