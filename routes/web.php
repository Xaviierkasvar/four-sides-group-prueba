<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

// Ruta raíz redirige a login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación (aplicando middleware guest directamente aquí)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Rutas para recuperación de contraseña
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::get('verify-code', [ForgotPasswordController::class, 'showVerificationCodeForm'])
        ->name('password.code');
    Route::post('verify-code', [ForgotPasswordController::class, 'verifyCode'])
        ->name('password.verify');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// Ruta de logout (requiere autenticación)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Rutas protegidas que requieren autenticación
Route::middleware(['auth'])->group(function () {
    // Ruta principal después del login (Catálogo de usuarios)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    
    // Resto de rutas de usuarios
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/photo', [UserController::class, 'editPhoto'])->name('users.edit.photo');
    Route::post('/users/{user}/photo', [UserController::class, 'updatePhoto'])->name('users.update.photo');
});