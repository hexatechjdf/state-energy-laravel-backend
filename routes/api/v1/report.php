<?php

use App\Http\Controllers\Api\V1\ReportController;
use Illuminate\Support\Facades\Route;

Route::post('/finance-report', [ReportController::class, 'generateReport']);
