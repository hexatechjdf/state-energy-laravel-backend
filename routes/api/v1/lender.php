<?php

use App\Http\Controllers\Api\V1\LenderController;
use Illuminate\Support\Facades\Route;

Route::apiResource('disposition', LenderController::class);
