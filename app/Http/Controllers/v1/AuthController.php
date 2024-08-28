<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            'email_or_username' => 'required',
            'password' => 'required'
        ]);

        $emailOrUsername = $request->input('email_or_username');
        $password = $request->input('password');

        $response = $this->authService->login($emailOrUsername, $password);

        return response()->json($response, $response['error'] ? Response::HTTP_UNAUTHORIZED : Response::HTTP_OK); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function register(Request $request): JsonResponse
    {
       $request->validate([
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'display_name' => 'required',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
        ]);

        $success = $this->authService->register($request->all());
        
        return response()->json($success, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function forgot_password(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }

    public function reset_password(Request $request)
    {

    }
}
