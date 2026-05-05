<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
use App\Models\District;
use App\Models\Thana;
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

    // Profile Routes
    Route::get('/profile/create', [ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile/store', [ProfileController::class, 'store'])->name('profile.store');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // // Submission Placeholder
    // Route::get('/submissions', function() {
    //     return "Submissions page coming soon!";
    // })->name('submissions.index');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/submissions/store', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions/{id}', [SubmissionController::class, 'show'])->name('submissions.show');
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


Route::get('/get-districts/{division_id}', function ($division_id) {
    return District::where('division_id', $division_id)->orderBy('name', 'asc')->get();
})->name('get.districts');

Route::get('/get-thanas/{district_id}', function ($district_id) {
    return Thana::where('district_id', $district_id)->orderBy('name', 'asc')->get();
})->name('get.thanas');
// Route::get('/', function () {
//     return view('dashboard/dashboard');
// });