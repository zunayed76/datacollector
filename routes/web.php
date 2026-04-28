<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

//Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () { return view('dashboard'); })->name('dashboard');
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/register', function() { return view('auth.register'); })->name('register.page');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/verify', function() { return view('auth.verify'); })->name('verify.page');
Route::post('/verify', [AuthController::class, 'verifyOtp'])->name('verify.otp');

// Forgot Password Flow
Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

// Reset Password Flow (The link from the email)
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');
// Route::get('/', function () {
//     return view('dashboard/dashboard');
// });