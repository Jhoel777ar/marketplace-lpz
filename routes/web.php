<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\TestVentaController;
use App\Livewire\MisCompras;
use App\Livewire\MetodoPago;
use App\Livewire\CarritoDetalle;
use App\Livewire\ProductoDetalle;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Rutas para Google Login
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

require __DIR__.'/auth.php';

Route::get('/test-venta', [TestVentaController::class, 'create'])->middleware('auth');
Route::get('/test-resena', [\App\Http\Controllers\TestReseñaController::class, 'create'])->middleware('auth');


Route::get('/productos/{producto}', ProductoDetalle::class)->name('productos.detalle');

Route::middleware(['auth'])->group(function () {
    Route::get('/carrito', CarritoDetalle::class)->name('carrito');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/metodo-pago', MetodoPago::class)->name('metodo.pago');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/mis-compras', MisCompras::class)->name('mis.compras');
});
