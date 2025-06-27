<?php

use App\Http\Controllers\Api\V1\CartController;
use Illuminate\Support\Facades\Route;

Route::apiResource('cart', CartController::class);
Route::get('userCart/clear-cart', [CartController::class, 'clear']);
Route::get('get-finance-apr-options', [CartController::class, 'getFinanceProviderAPROptions']);
Route::get('calculate-financing-amount', [CartController::class, 'calculateFinancingAmount']);
