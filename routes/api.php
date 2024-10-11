<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\CommentController;
use App\Http\Controllers\v1\MediaController;
use App\Http\Controllers\v1\NotificationController;
use App\Http\Controllers\v1\PostController;
use App\Http\Controllers\v1\TagController;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => ['force.json']], function () {
    // Authentication Routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register');
        Route::post('/forgot-password', 'sendResetLinkEmail')->middleware('throttle:1, 1');
        Route::post('/reset-password', 'reset_password')->name('password.reset');
        Route::post('/logout', 'logout')->middleware('auth');
        Route::get('/me', 'me')->middleware('auth');
        Route::get('/login/google', [AuthController::class, 'redirectToGoogle']);
        Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->middleware('web');
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
            Route::get('/{slug}', 'show');
            Route::get('', 'index');
            Route::post('', 'store')->middleware(['auth']);
            Route::post('/{id}/vote', 'vote')->middleware(['auth']);
        });
    });

    // Tags Routes
    Route::prefix('tags')->group(function () {
        Route::get('/search', [TagController::class, 'search'])->name('tags.search');
    });

    // Protected Routes
    Route::middleware(['auth'])->group(function () {
        // User Routes
        Route::prefix('users')->group(function () {
            Route::get('/{username}', function ($username) {
                return response()->json(['username' => $username]);
            })->name('user.profile');
        });
    });

    // comments route
    Route::prefix('posts/{postId}/comments')->group(function () {
        Route::controller(CommentController::class)->group(function () {
            Route::post('', 'store')->middleware('auth');
            Route::put('{id}', 'update')->middleware('auth');
            Route::get('', 'showParent');
            Route::get('/{parentId}/replies', 'showChild');
            Route::delete('{id}', 'destroy')->middleware('auth');
        });
    });

    Route::post('/upload', [MediaController::class, 'upload'])->middleware('auth');
    Route::get('/get-object', [MediaController::class, 'getObject']);

    Route::middleware(['admin'])->group(function () {
        Route::prefix('admin')->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::get('users', 'index');
            });
        });
    });

    Route::group(['prefix' => 'notifications', 'middleware' => 'auth'], function () {
        Route::controller(NotificationController::class)->group(function () {
            Route::get('', 'index');
            Route::post('mark-as-read', 'markAsRead');
        });
    });
});
Route::fallback(function () {
    abort(404, 'API resource not found');
});
