<?php

use App\Http\Controllers\Api\V1\OrderController;
use Illuminate\Support\Facades\Route;

Route::apiResource('order', OrderController::class);
Route::get('order/get-order-by-appointment/{appointmentId}', [OrderController::class, 'getOrderByAppointment']);