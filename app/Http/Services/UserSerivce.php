<?php

namespace App\Http\Services;

use App\Models\Profile;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
    public function login($credentials)
    {
        if (Auth::attempt($credentials)) {
            Auth::user();
            return true;
        }

        return [
            'errors' => [
                'email' => ['Email or password is not true'],
                'password' => []
            ]
        ];
    }
}
