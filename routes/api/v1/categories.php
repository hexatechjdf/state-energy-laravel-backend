<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CategoryController;

Route::apiResource('categories', CategoryController::class);
