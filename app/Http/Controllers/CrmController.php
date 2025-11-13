<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessRefreshToken;
use App\Jobs\UpdateRefreshToken;
use Illuminate\Support\Facades\Log;

class CrmController extends Controller
{

    public function __construct() {}
    public function refreshCrmTokens()
    {
        dispatch(new UpdateRefreshToken())->onQueue('refresh_token');
        Log::info('Token refresh job dispatched (page 1) via URL.');
        return response()->json([
            'status' => 'success',
            'message' => 'CRM token refresh job dispatched successfully (page 1).'
        ]);
    }
}
