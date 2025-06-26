<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Auth::routes();
Route::get('/', [HomeController::class, 'root'])->name('root')->middleware('auth');
Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth']], function () {

    Route::get('setting', [SettingController::class, 'index'])->name('setting');
    Route::post('/setting/save', [SettingController::class, 'store'])->name('setting.save');

    Route::get('/email-templates/list', [SettingController::class, 'emailTemplateList'])->name('email-templates.list');


    Route::post('/store', [AdminController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [AdminController::class, 'show'])->name('users.show');
    Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.delete');
    Route::post('/update-profile/{id}', [AdminController::class, 'update'])->name('update.profile');
    Route::get('index', [AdminController::class, 'index'])->name('user.index');
    Route::post('/get-table-data', [AdminController::class, 'getTableData'])->name('user.table-data');

    Route::group(['as' => 'category.', 'prefix' => 'category', 'middleware' => ['auth']], function () {
        Route::get('/index', [CategoryController::class, 'index'])->name('index');
        Route::post('/get-table-data', [CategoryController::class, 'getTableData'])->name('table-data');

        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::post('/update', [CategoryController::class, 'update'])->name('update');


    });

});