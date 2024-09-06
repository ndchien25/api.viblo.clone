<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendResetLinkEmailRequest;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService){}

    /**
     * Attempt to authenticate the user.
     * @param  string  $emailOrUsername
     * @param  string  $password
     * @return array
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $response = $this->authService->login(
            $request->input('email_or_username'),
            $request->input('password')
        );
        return response()->json($response, $response['error'] ? Response::HTTP_UNAUTHORIZED : Response::HTTP_OK);
    }

    /**
     * Handler User Register
     * @param array $credentials
     * @return mixed
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $success = $this->authService->register($request->all());
        return response()->json($success, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function sendResetLinkEmail(SendResetLinkEmailRequest $request): JsonResponse
    {
        $status = $this->authService->sendResetLinkEmail($request->only('email'));

        return $status === Password::RESET_LINK_SENT ? response()->json([
            'message' => __($status)
        ], Response::HTTP_OK) : response()->json([
            'message' => __($status)
        ], Response::HTTP_BAD_REQUEST);
    }

    public function reset_password(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword($request->only('email', 'password', 'c_password', 'token'));
        return $status === Password::RESET_LINK_SENT ? response()->json([
            'message' => __($status)
        ], Response::HTTP_OK) : response()->json([
            'message' => __($status)
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Logout user
     */
    public function logout(): JsonResponse
    {
        Auth::guard('web')->logout();
        return response()->json([
            'error' => false,
            'message' => 'Successfully logged out!',
        ]);
    }
}
