<?php

namespace App\Models;

use App\Helpers\CRM;
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
    public function urefresh(): bool
    {
        $is_refresh = false;
        try {
            list($is_refresh, $token) = CRM::getRefreshToken($this->user_id, $this, true);
        } catch (\Exception $e) {
            return 500;
        }
        return $is_refresh;
    }
}
