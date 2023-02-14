<?php

use App\Http\Controllers\FacilityController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Role;
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
Route::get('/unauthorized', function () {
  return response()->json([
    'status' => 'fail',
    'error'=> 'Unauthorized!',
  ], 401);
})->name('unauthorized');


Route::middleware(['auth'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('{id}', 'getUser');
            Route::get('/', 'list');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });

    Route::controller(FacilityController::class)->group(function () {
        Route::prefix('facilities')->group(function () {
            Route::get('{id}', 'getFacility');
            Route::get('/', 'list');
            Route::post('save', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });

    Route::controller(RoleController::class)->group(function () {
        Route::prefix('roles')->group(function () {
            Route::get('{id}', 'getRole');
            Route::get('/', 'list');
            Route::post('save', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });
    });
});