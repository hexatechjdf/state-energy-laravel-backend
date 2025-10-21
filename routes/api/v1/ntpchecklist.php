<?php

use App\Http\Controllers\Api\V1\NTPCheckListController;
use Illuminate\Support\Facades\Route;

Route::apiResource('ntp-checklist', NTPCheckListController::class);
