<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\StatusController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', App\Http\Controllers\Api\RegisterController::class)->name('register');

/**
 * route "/login"
 * @method "POST"
 */
Route::post('/login', App\Http\Controllers\Api\LoginController::class)->name('login');

/**
 * route "/user"
 * @method "GET"
 */
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/logout', App\Http\Controllers\Api\LogoutController::class)->name('logout');
// Route::put('/orders/{order}', [OrderController::class, 'updateStatus'])->name('orders.updateStatus')
Route::put('/update-status/{id}', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
Route::get('/waiting-list', [OrderController::class, 'waitingList'])->name('orders.waitingList');

Route::resource('stocks',StockController::class);
Route::resource('orders',OrderController::class);
Route::resource('actions',ActionController::class);
Route::resource('statuss',StatusController::class);




