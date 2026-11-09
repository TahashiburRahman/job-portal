<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/jobs', [FrontendController::class, 'jobs'])->name('jobs.index');
Route::get('/jobs/{slug}', [FrontendController::class, 'showJob'])->name('jobs.show');
Route::get('/companies', [FrontendController::class, 'companies'])->name('companies.index');
Route::get('/companies/{slug}', [FrontendController::class, 'showCompany'])->name('companies.show');
Route::get('/locations/divisions/{division}/districts', [FrontendController::class, 'districts'])->name('locations.districts');
Route::get('/locations/districts/{district}/upazilas', [FrontendController::class, 'upazilas'])->name('locations.upazilas');
Route::view('/about', 'frontend.page', ['page' => 'about'])->name('about');
Route::view('/contact', 'frontend.page', ['page' => 'contact'])->name('contact');
Route::view('/articles', 'frontend.page', ['page' => 'articles'])->name('articles');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
    Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'employer' => redirect()->route('employer.dashboard'),
        default => redirect()->route('candidate.dashboard'),
    };
})->middleware('auth')->name('dashboard');
