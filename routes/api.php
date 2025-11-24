<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['api'])
    ->group(function () {
        require base_path('routes/api/v1/auth.php');
        require base_path('routes/api/v1/user.php');
        require base_path('routes/api/v1/setting.php');
        Route::middleware(['auth:sanctum'])->group(function () {
            require base_path('routes/api/v1/categories.php');
            require base_path('routes/api/v1/cart.php');
            require base_path('routes/api/v1/order.php');
            require base_path('routes/api/v1/disposition.php');
            require base_path('routes/api/v1/lender.php');
            require base_path('routes/api/v1/ntpchecklist.php');
            require base_path('routes/api/v1/report.php');
        });
    });
