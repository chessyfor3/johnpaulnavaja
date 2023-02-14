<?php

use App\Http\Controllers\FacilityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/login', [UserController::class, 'authenticate']);
Route::post('/register', [UserController::class, 'store']);


Route::middleware(['auth'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('{id}', 'getUser');
            Route::get('list', 'list');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });

    Route::controller(FacilityController::class)->group(function () {
        Route::prefix('facilities')->group(function () {
            Route::get('{id}', 'getFacility');
            Route::get('list', 'list');
            Route::post('save', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });

    Route::controller(Role::class)->group(function () {
        Route::prefix('roles')->group(function () {
            Route::get('{id}', 'getRole');
            Route::get('list', 'list');
            Route::post('save', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });
});