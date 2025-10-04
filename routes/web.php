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
    Route::get('/pos', [\App\Http\Controllers\Web\PosController::class, 'index'])->name('pos.index')->middleware('role:cajero');
    Route::post('/pos/orders', [\App\Http\Controllers\Web\PosController::class, 'createOrder'])->name('pos.createOrder')->middleware('role:cajero');
    Route::post('/pos/orders/{order}/items', [\App\Http\Controllers\Web\PosController::class, 'addItem'])->name('pos.addItem')->middleware('role:cajero');
    Route::delete('/pos/orders/{order}/items/{item}', [\App\Http\Controllers\Web\PosController::class, 'removeItem'])->name('pos.removeItem')->middleware('role:cajero');
    Route::patch('/pos/orders/{order}/close', [\App\Http\Controllers\Web\PosController::class, 'closeOrder'])->name('pos.closeOrder')->middleware('role:cajero');
    Route::get('/pos/reservations/{id}', [\App\Http\Controllers\Web\PosController::class, 'getReservation'])->name('pos.getReservation')->middleware('role:cajero');


    // Rutas de administración (solo para admin)
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        // POS para admin
        Route::get('/pos', [\App\Http\Controllers\Web\PosController::class, 'index'])->name('pos.index');
        Route::post('/pos', [\App\Http\Controllers\Web\PosController::class, 'store'])->name('pos.store');
        Route::get('/pos/product/{product_id}', [\App\Http\Controllers\Web\PosController::class, 'getProduct'])->name('pos.product');

        // Gestión de ventas
        Route::resource('sales', \App\Http\Controllers\Web\SaleController::class);

        // Gestión de reservas
        Route::resource('reservations', \App\Http\Controllers\Web\ReservationController::class);
    });

    // Exportaciones (usando el controlador API existente)
    Route::get('/dashboard/export/sales', [\App\Http\Controllers\DashboardController::class, 'exportSales'])->name('dashboard.export.sales');
    Route::get('/dashboard/export/reservations', [\App\Http\Controllers\DashboardController::class, 'exportReservations'])->name('dashboard.export.reservations');
});
