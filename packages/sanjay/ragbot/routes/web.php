<?php

use Illuminate\Support\Facades\Route;
use Sanjay\Ragbot\Http\Controllers\Auth\LoginController;
use Sanjay\Ragbot\Http\Controllers\Auth\RegisterController;
use Sanjay\Ragbot\Http\Controllers\Auth\Tenant\LoginController as TenantLoginController;
use Sanjay\Ragbot\Http\Controllers\Auth\Tenant\RegisterController as TenantRegisterController;
use Sanjay\Ragbot\Livewire\Dashboard;

Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'package' => 'sanjay/ragbot',
    ]);
});

// Platform Authentication (Normal)
Route::group(['middleware' => ['web']], function () {
    Route::middleware(['guest'])->group(function () {
        Route::get('register', [RegisterController::class, 'create'])->name('register');
        Route::post('register', [RegisterController::class, 'store'])->name('register.store');
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.store');
    });

    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware(['auth'])->get('dashboard', function () {
        return view('ragbot::auth.platform.dashboard');
    })->name('platform.dashboard');
});

// Tenant Authentication
Route::group([
    'prefix' => 'tenant/{project_slug}',
    'middleware' => ['web', Sanjay\Ragbot\Http\Middleware\ResolveProjectFromSlug::class, Sanjay\Ragbot\Http\Middleware\SetRagbotAuthGuard::class],
], function () {
    Route::middleware(['guest:ragbot'])->group(function () {
        Route::get('register', [TenantRegisterController::class, 'create'])->name('tenant.register');
        Route::post('register', [TenantRegisterController::class, 'store'])->name('tenant.register.store');
        Route::get('login', [TenantLoginController::class, 'create'])->name('tenant.login');
        Route::post('login', [TenantLoginController::class, 'store'])->name('tenant.login.store');
    });

    Route::post('logout', [TenantLoginController::class, 'destroy'])->name('tenant.logout');

    Route::middleware(['ragbot.authenticated'])->group(function () {
        Route::get('dashboard', Dashboard::class)->name('dashboard');

        Route::get('documents', function () {
            return view('ragbot::layouts.dashboard', ['slot' => 'Documents coming soon']);
        })->name('documents');

        Route::get('settings', function () {
            return view('ragbot::layouts.dashboard', ['slot' => 'Settings coming soon']);
        })->name('settings');
    });
});
