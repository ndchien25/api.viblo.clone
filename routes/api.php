<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password' , [AuthController::class, 'sendResetLinkEmail'])->middleware('throttle:1, 1');
    Route::post('/reset-password', [AuthController::class, 'reset_password'])->name('password.reset');

    // Verify email
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Resend link to verify email
    Route::post('/email/verify/resend', [VerifyEmailController::class, 'resendVerificationEmail'])->middleware(['throttle:6,1'])->name('verification.send');

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::prefix('/u')->group(function () {
            Route::get('/{username}', function ($username) {
                return response()->json(['username' => $username]);
            })->name('user.profile');
        });
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});
