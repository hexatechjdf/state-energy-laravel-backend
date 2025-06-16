<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Auth::routes();
Route::get('/', [HomeController::class, 'root'])->name('root')->middleware('auth');
Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth']], function () {
    Route::post('/update-profile', [AdminController::class, 'update'])->name('update.profile');
    Route::get('setting', [SettingController::class, 'index'])->name('setting');
    Route::post('/setting/save', [SettingController::class, 'store'])->name('setting.save');

   
});