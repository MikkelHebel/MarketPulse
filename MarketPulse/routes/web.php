<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;

Route::get('/', fn() => redirect('dashboard'));
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/chart/data', [DashboardController::class, 'chartData']);

Route::middleware('auth')->group(function () {
    Route::get('notifications/poll', [NotificationController::class, 'poll']);
});
