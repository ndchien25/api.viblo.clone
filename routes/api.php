<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    $user = User::find(1);

    $token = $user->createToken('Token Name')->plainTextToken;
    return $token;
});
