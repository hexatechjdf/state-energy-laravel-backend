<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;

Route::apiResource('/users', UserController::class);
Route::post('/user/change-password', [UserController::class, 'changePassword'])->middleware('auth:sanctum');
Route::get('/user/get-appointment', [UserController::class, 'getAppointment'])->middleware('auth:sanctum');
Route::get('/user/get-hl-user', [UserController::class, 'getHLUsers']);
Route::get('/user/get-crm-contact', [UserController::class, 'getCRMContact']);
Route::post('/user/appointment/send-disposition', [UserController::class, 'sendDisposition'])->middleware('auth:sanctum');
