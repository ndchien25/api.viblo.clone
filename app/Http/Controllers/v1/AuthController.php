<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendResetLinkEmailRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API endpoints for user authentication"
 * )
 */
class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *          required=true,
     *              @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(response=200, description="Login successful", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Invalid Input", @OA\JsonContent()),
     * )
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
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="User registration",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *     @OA\Response(response=201, description="User registered successfully", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Invalid Input", @OA\JsonContent()),
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $success = $this->authService->register($request->all());
        return response()->json($success, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/forgot-password",
     *     summary="Send reset link email",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="nona.upton@example.org")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Reset link sent", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Bad Request", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Invalid email", @OA\JsonContent()),
     *     @OA\Response(response=429, description="Many Request", @OA\JsonContent()),
     * )
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

    /**
     * @OA\Post(
     *     path="/api/v1/reset-password",
     *     summary="Reset user password",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ResetPasswordRequest")
     *     ),
     *     @OA\Response(response=200, description="Password reset successfully"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=422, description="Invalid input"),
     * )
     */
    public function reset_password(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword($request->only('email', 'password', 'c_password', 'token'));
        return $status === Password::PASSWORD_RESET ? response()->json([
            'message' => __($status)
        ], Response::HTTP_OK) : response()->json([
            'message' => __($status)
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Logout user",
     *     tags={"Authentication"},
     *     @OA\Response(response=200, description="Successfully logged out", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     * )
     */
    public function logout(): JsonResponse
    {
        Auth::logout();
        return response()->json([
            'error' => false,
            'message' => 'Successfully logged out!',
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/me",
     *     summary="Get authenticated user",
     *     tags={"Authentication"},
     *     @OA\Response(response=200, description="Authenticated user data",@OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json(['authenticated' => true, 'user' => new UserResource($request->user())]);
    }
}
