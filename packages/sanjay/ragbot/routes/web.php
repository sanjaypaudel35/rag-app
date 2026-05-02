<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Sanjay\Ragbot\Livewire\Dashboard;

Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'package' => 'sanjay/ragbot',
    ]);
});

// Registration
Route::get('register', [RegisteredUserController::class, 'create'])
    ->middleware(['guest:ragbot'])
    ->name('register');

Route::post('register', [RegisteredUserController::class, 'store'])
    ->middleware(['guest:ragbot']);

// Login
Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->middleware(['guest:ragbot'])
    ->name('login');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest:ragbot']);

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// Password Reset
Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware(['guest:ragbot'])
    ->name('password.request');

Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware(['guest:ragbot'])
    ->name('password.email');

Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware(['guest:ragbot'])
    ->name('password.reset');

Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->middleware(['guest:ragbot'])
    ->name('password.update');

Route::middleware(['ragbot.authenticated'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    Route::get('documents', function () {
        return view('ragbot::layouts.dashboard', ['slot' => 'Documents coming soon']);
    })->name('documents');

    Route::get('settings', function () {
        return view('ragbot::layouts.dashboard', ['slot' => 'Settings coming soon']);
    })->name('settings');
});
