<?php

use App\Http\Controllers\Candidate\CandidateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:employee'])->prefix('candidate')->name('candidate.')->group(function () {
    Route::get('/dashboard', [CandidateController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [CandidateController::class, 'profileForm'])->name('profile');
    Route::put('/profile', [CandidateController::class, 'updateProfile'])->name('profile.update');
    Route::get('/resumes/{profile}/download', [CandidateController::class, 'downloadResume'])->name('resume.download');
    Route::post('/jobs/{job}/apply', [CandidateController::class, 'apply'])->name('jobs.apply');
    Route::get('/applications', [CandidateController::class, 'applications'])->name('applications');
    Route::get('/saved-jobs', [CandidateController::class, 'savedJobs'])->name('saved-jobs');
    Route::post('/jobs/{job}/save', [CandidateController::class, 'saveJob'])->name('jobs.save');
    Route::delete('/saved-jobs/{savedJob}', [CandidateController::class, 'removeSavedJob'])->name('saved-jobs.destroy');
});
