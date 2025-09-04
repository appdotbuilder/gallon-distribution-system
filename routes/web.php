<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Admin\RequestApprovalController;
use App\Http\Controllers\Admin\RequestPreparationController;
use App\Http\Controllers\Admin\RequestExportController;
use App\Http\Controllers\GallonSystemController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health-check');

// Public gallon system routes (no authentication required)
Route::get('/', [GallonSystemController::class, 'index'])->name('home');
Route::post('/gallon-system', [GallonSystemController::class, 'store'])->name('gallon-system.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Employee management (HR Admin only)
        Route::resource('employees', EmployeeController::class);

        // Request management
        Route::resource('requests', RequestController::class)->only(['index', 'show']);
        Route::resource('requests.approvals', RequestApprovalController::class)->only(['store']);
        Route::resource('requests.preparations', RequestPreparationController::class)->only(['store']);
        Route::resource('request-exports', RequestExportController::class)->only(['show']);
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
