<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Attempt to authenticate the user.
     *
     * @param  string  $emailOrUsername
     * @param  string  $password
     * @return array
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email_or_username' => 'required|string',
            'password' => 'required|string'
        ]);

        $emailOrUsername = $request->input('email_or_username');
        $password = $request->input('password');

        $response = $this->authService->login($emailOrUsername, $password);

        return response()->json($response, $response['error'] ? Response::HTTP_UNAUTHORIZED : Response::HTTP_OK);
    }

    /**
     * Handler User Register
     * @param array $credentials
     * @return mixed
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'display_name' => 'required|string',
            'password' => 'required|string|min:8',
            'c_password' => 'required|string|same:password',
        ]);

        $success = $this->authService->register($request->all());

        return response()->json($success, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = $this->authService->sendResetLinkEmail($request->only('email'));
        
        return $status === Password::RESET_LINK_SENT ? response()->json([
            'message' => __($status)
        ], Response::HTTP_OK) : response()->json([
            'message' => __($status)
        ], Response::HTTP_BAD_REQUEST);
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
            'c_password' => 'required|string|same:password'
        ]);

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
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
 
        return response()->json([
            'error' => false,
            'message' => 'Successfully logged out!',
        ]);
    }
}
