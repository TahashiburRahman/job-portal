<?php

use App\Http\Controllers\Employer\EmployerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/dashboard', [EmployerController::class, 'dashboard'])->name('dashboard');
    Route::get('/company-profile', [EmployerController::class, 'companyForm'])->name('company');
    Route::put('/company-profile', [EmployerController::class, 'updateCompany'])->name('company.update');
    Route::get('/jobs', [EmployerController::class, 'jobs'])->name('jobs');
    Route::get('/jobs/create', [EmployerController::class, 'createJob'])->name('jobs.create');
    Route::post('/jobs', [EmployerController::class, 'storeJob'])->name('jobs.store');
    Route::get('/jobs/{job}/edit', [EmployerController::class, 'editJob'])->name('jobs.edit');
    Route::put('/jobs/{job}', [EmployerController::class, 'updateJob'])->name('jobs.update');
    Route::patch('/jobs/{job}/close', [EmployerController::class, 'closeJob'])->name('jobs.close');
    Route::get('/jobs/{job}/applicants', [EmployerController::class, 'applicants'])->name('jobs.applicants');
    Route::get('/jobs/{job}/applicants/{profile}/resume', [EmployerController::class, 'downloadApplicantResume'])->name('jobs.applicants.resume');
});
