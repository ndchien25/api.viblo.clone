<?php

namespace App\Http\Services;

use App\Models\User;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthService extends BaseService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handler User Login
     * @param array $credentials
     * @return mixed
     */
    public function login($emailOrUsername = '', $password = ''): array
    {
        $fieldType = filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [$fieldType => $emailOrUsername, 'password' => $password];

        if (Auth::attempt($credentials)) {
            return [
                'error' => false,
                'message' => 'Login successful!',
            ];
        } else {
            return [
                'error' => true,
                'message' => 'Wrong email/username or password!',
            ];
        }
    }

    public function register($payload = []): array
    {
        $payload['password'] = bcrypt($payload['password']);
        $user = User::create($payload);
        $success['token'] =  $user->createToken('authToken')->plainTextToken;
        $success['display_name'] =  $user->display_name;
        $success['username'] = $user->username;

        return $success;
    }

    public function forgot_password($payload = [])
    {
        $status = Password::sendResetLink(
            $payload['email'],
        );
        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }
}
