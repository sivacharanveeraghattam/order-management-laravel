<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;

Route::prefix('v1')->group(function () {

    // Public
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);

    // Protected – session based
    Route::middleware('auth:web')->group(function () {
        Route::get('me',      [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});


/* Route::middleware(['web', 'auth'])->group(function () { */
Route::apiResource('products', ProductController::class);
// Orders
Route::apiResource('orders', OrderController::class)->only(['index', 'store']);
/* }); */
