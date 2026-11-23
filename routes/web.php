<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Página principal
Route::get('/', function () {
    return redirect()->route('login');
});

// Inicio de sesión
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

// Registro de empleados
Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.post');

// Verificación del administrador
Route::get('/verify-code', [AuthController::class, 'showVerifyCode'])
    ->name('verify-code');

Route::post('/verify-code', [AuthController::class, 'verifyCode'])
    ->name('verify-code.post');

// Cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Panel exclusivo del administrador
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'rol:administrador'])
    ->name('admin.dashboard');

// Panel exclusivo del empleado
Route::get('/empleado/dashboard', function () {
    return view('empleado.dashboard');
})->middleware(['auth', 'rol:empleado'])
    ->name('empleado.dashboard');
