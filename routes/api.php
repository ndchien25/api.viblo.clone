<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\CommentController;
use App\Http\Controllers\v1\PostController;
use App\Http\Controllers\v1\TagController;
use App\Http\Controllers\v1\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Authentication Routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register');
        Route::post('/forgot-password', 'sendResetLinkEmail')->middleware('throttle:1, 1');
        Route::post('/reset-password', 'reset_password')->name('password.reset');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
        Route::get('/me', 'me')->middleware('auth:sanctum');
    });

    // Email Verification Routes
    Route::prefix('email/verify')->group(function () {
        Route::get('/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
        Route::post('/resend', [VerifyEmailController::class, 'resendVerificationEmail'])
            ->middleware(['throttle:6,1'])
            ->name('verification.send');
    });

    // Post Routes
    Route::prefix('posts')->group(function () {
        Route::controller(PostController::class)->group(function () {
            Route::get('/{slug}', 'show')->name('posts.show');
            Route::post('', 'store')->name('posts.store')->middleware(['auth:sanctum']);
            Route::post('/{id}/vote', 'vote')->name('posts.vote')->middleware(['auth:sanctum']);
        });
    });

    // Tags Routes
    Route::prefix('tags')->group(function () {
        Route::get('/search', [TagController::class, 'search'])->name('tags.search');
    });

    // Protected Routes
    Route::middleware(['auth:sanctum'])->group(function () {
        // User Routes
        Route::prefix('users')->group(function () {
            Route::get('/{username}', function ($username) {
                return response()->json(['username' => $username]);
            })->name('user.profile');
        });
    });

    // comments route
    Route::prefix('comments')->group(function() {
        Route::controller(CommentController:: class)->group(function () {
            Route::post('', 'store')->name('comments.store');
            Route::get('{post_id}','show');
        });
    });
});
