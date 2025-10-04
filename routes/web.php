<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

// Página de inicio
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'webLogin'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'webRegister'])->name('register.post');
});

// Rutas protegidas
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard')->middleware('role:admin');

    // Rutas adicionales para diferentes roles
    Route::get('/pos', function () {
        return view('pos.index');
    })->name('pos.index')->middleware('role:cajero');

    Route::get('/reservations', [DashboardController::class, 'index'])->name('reservations.index'); // Clientes usan el mismo dashboard

    // Exportaciones (usando el controlador API existente)
    Route::get('/dashboard/export/sales', [\App\Http\Controllers\DashboardController::class, 'exportSales'])->name('dashboard.export.sales');
    Route::get('/dashboard/export/reservations', [\App\Http\Controllers\DashboardController::class, 'exportReservations'])->name('dashboard.export.reservations');
});
