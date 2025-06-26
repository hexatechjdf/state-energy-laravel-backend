<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    protected $appends = ['avatar_url'];
    const ROLE_ADMIN = 1;
    const ROLE_LOCATION = 2;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'dial_code',
        'city',
        'zip_code',
        'country',
        'contact_id',
        'user_id',
        'email',
        'avatar',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function settings()
    {
        return $this->hasMany(Setting::class);
    }
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return null;
        }

        // Choose appropriate disk
        $disk = 'public';

        return Storage::disk($disk)->url($this->avatar);
    }
    public function getSpecificSettings(array $keys)
    {
        return $this->settings()->whereIn('key', $keys)->pluck('value', 'key');
    }
}
