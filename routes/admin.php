<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\TaxonomyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs');
    Route::patch('/jobs/{job}/status', [AdminController::class, 'updateJobStatus'])->name('jobs.status');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
    Route::patch('/companies/{company}/verification', [AdminController::class, 'toggleCompanyVerification'])->name('companies.verification');
    Route::get('/classifications', [TaxonomyController::class, 'index'])->name('classifications');
    Route::post('/classifications', [TaxonomyController::class, 'store'])->name('classifications.store');
    Route::patch('/classifications/{type}/{id}', [TaxonomyController::class, 'update'])->name('classifications.update');
    Route::delete('/classifications/{type}/{id}', [TaxonomyController::class, 'destroy'])->name('classifications.destroy');
    Route::get('/locations', [LocationController::class, 'index'])->name('locations');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
    Route::patch('/locations/{type}/{id}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{type}/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');
});
