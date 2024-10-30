<?php

namespace App\Http\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthService extends BaseService
{
    public function login($emailOrUsername = '', $password = ''): array
    {
        $fieldType = filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$fieldType => $emailOrUsername, 'password' => $password];

        if (!Auth::attempt($credentials)) {
            return [
                'error' => true,
                'message' => 'Wrong email/username or password!',
                'verified' => true,
            ];
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            return [
                'error' => true,
                'message' => 'Email not verified!',
                'verified' => false,
            ];
        }

        return [
            'error' => false,
            'message' => 'Login successful!',
            'verified' => true,
            'user' => new UserResource($user)
        ];
    }

    public function register($payload = []): array
    {
        $payload['password'] = bcrypt($payload['password']);
        unset($payload['c_password']);
        $user = User::create($payload);
        event(new Registered($user));
        
        return [
            'username' => $user->username,
            'email' => $user->email,
        ];
    }

    public function sendResetLinkEmail($payload = [])
    {
        return Password::sendResetLink($payload);
    }

    public function resetPassword($payload = [])
    {
        $status = Password::reset(
            $payload,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->setRememberToken(Str::random(60));
                $user->save();
            }
        );
        return $status;
    }
}
