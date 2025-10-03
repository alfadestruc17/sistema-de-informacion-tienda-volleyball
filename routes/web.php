<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/auth/register', [AuthController::class, 'register']);
Route::post('/api/auth/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::apiResource('/api/courts', \App\Http\Controllers\CourtController::class)->middleware('role:admin');
    Route::get('/api/availability', [\App\Http\Controllers\CourtController::class, 'availability']);
    Route::apiResource('/api/reservations', \App\Http\Controllers\ReservationController::class);
    Route::apiResource('/api/products', \App\Http\Controllers\ProductController::class);
    Route::apiResource('/api/orders', \App\Http\Controllers\OrderController::class)->except(['update', 'destroy']);
    Route::post('/api/orders/{order}/items', [\App\Http\Controllers\OrderController::class, 'addItem']);
    Route::delete('/api/orders/{order}/items/{item}', [\App\Http\Controllers\OrderController::class, 'removeItem']);
    Route::patch('/api/orders/{order}/close', [\App\Http\Controllers\OrderController::class, 'close']);
});
