<?php

use App\Http\Controllers\v1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgot_password']);
    Route::post('/reset-password', [AuthController::class, 'reset_password']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('/u')->group(function () {
            Route::get('/{username}', function ($username) {
                return response()->json(['username' => $username]);
            })->name('user.profile');
        });
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});
