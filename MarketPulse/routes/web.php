<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ThresholdController;
use App\Http\Controllers\SearchController;

Route::get('/', fn() => redirect('dashboard'));
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/chart/data', [DashboardController::class, 'chartData']);

Route::middleware('auth')->group(function () {
    Route::get('/thresholds', [ThresholdController::class, 'index'])->name('thresholds');
    Route::post('/thresholds', [ThresholdController::class, 'store'])->name('thresholds.store');
    Route::delete('/thresholds/{threshold}', [ThresholdController::class, 'destroy'])->name('thresholds.destroy');

    Route::get('/notifications/poll', [NotificationController::class, 'poll']);

    Route::get('/search', [SearchController::class, 'show'])->name('search');
    Route::get('/search/data', [SearchController::class, 'data'])->name('search.data');
});
