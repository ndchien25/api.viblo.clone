<?php

namespace App\Http\Services;

use App\Models\User;
use App\Repositories\User\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthService extends BaseService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login($emailOrUsername = '', $password = ''): array
    {
        $fieldType = filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [$fieldType => $emailOrUsername, 'password' => $password];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (!$user->email_verified_at) {
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
            ];
        } else {
            return [
                'error' => true,
                'message' => 'Wrong email/username or password!',
                'verified' => true,
            ];
        }
    }

    public function register($payload = []): array
    {
        $payload['password'] = bcrypt($payload['password']);
        $user = $this->userRepository->create($payload);
        event(new Registered($user));
        $success['display_name'] =  $user->display_name;
        $success['username'] = $user->username;

        return $success;
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
