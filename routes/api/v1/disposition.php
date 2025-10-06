<?php

use App\Http\Controllers\Api\V1\DispositionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('disposition', DispositionController::class);
