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
});
