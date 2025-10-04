<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/courts', \App\Http\Controllers\CourtController::class)->middleware('role:admin');
    Route::get('/availability', [\App\Http\Controllers\CourtController::class, 'availability']);
    Route::apiResource('/reservations', \App\Http\Controllers\ReservationController::class);
    Route::apiResource('/products', \App\Http\Controllers\ProductController::class);
    Route::apiResource('/orders', \App\Http\Controllers\OrderController::class)->except(['update', 'destroy']);
    Route::post('/orders/{order}/items', [\App\Http\Controllers\OrderController::class, 'addItem']);
    Route::delete('/orders/{order}/items/{item}', [\App\Http\Controllers\OrderController::class, 'removeItem']);
    Route::patch('/orders/{order}/close', [\App\Http\Controllers\OrderController::class, 'close']);

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/kpis', [\App\Http\Controllers\DashboardController::class, 'kpis']);
        Route::get('/weekly-calendar', [\App\Http\Controllers\DashboardController::class, 'weeklyCalendar']);
        Route::get('/top-products', [\App\Http\Controllers\DashboardController::class, 'topProducts']);
        Route::get('/weekly-revenue', [\App\Http\Controllers\DashboardController::class, 'weeklyRevenue']);
        Route::get('/stats', [\App\Http\Controllers\DashboardController::class, 'stats']);
    });
});