<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CarritoController;
use Livewire\Volt\Volt;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\TestVentaController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/home', [ProductoController::class, 'MostrarProducto'])->name('inicio');

Route::get('dashboard', [ProductoController::class, 'MostrarProducto'])
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


Route::middleware(['auth'])->group(function () {
    Route::post('/carrito/agregar/{id}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::get('/carrito', [CarritoController::class, 'ver'])->name('carrito.ver');
    Route::post('/carrito/actualizar/{id}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
    Route::post('/carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
    //Route::get('/carrito/contador', [CarritoController::class, 'contadorJson'])->name('carrito.contador');

    Route::post('/carrito/aplicar-cupon', [App\Http\Controllers\CarritoController::class, 'aplicarCupon'])->name('carrito.aplicarCupon');

});









require __DIR__.'/auth.php';

Route::get('/test-venta', [TestVentaController::class, 'create'])->middleware('auth');
Route::get('/test-resena', [\App\Http\Controllers\TestReseñaController::class, 'create'])->middleware('auth');

use App\Livewire\ProductoDetalle;

Route::get('/productos/{producto}', ProductoDetalle::class)->name('productos.detalle');
