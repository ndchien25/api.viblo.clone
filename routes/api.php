<?php

use App\Http\Controllers\v1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgot_password']);
    Route::post('/reset-password', [AuthController::class, 'reset_password']);
    Route::prefix('/u')->group(function () {
        Route::middleware('auth:sanctum')->group(function() {
            Route::get('/{username}', function ($username) {
                return response()->json(['username' => $username]);
            })->name('user.profile');
        });     
    });

});
