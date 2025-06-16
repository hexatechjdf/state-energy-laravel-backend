<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SettingController;

Route::apiResource('setting', SettingController::class);
