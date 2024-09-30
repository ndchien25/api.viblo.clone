<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Verification",
 *     description="API endpoints for email verification"
 * )
 */
class VerifyEmailController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/email/verify/{id}/{hash}",
     *     summary="Verify user email",
     *     tags={"Verification"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the user to verify",
     *         @OA\Schema(type="integer")
     *     ),
     *      @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         required=true,
     *         description="The hash used for verifying the user's email",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirects after verification",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *     )
     * )
     */
    public function __invoke(Request $request)
    {
        $hash = $request->route('hash');
        $user =  User::find($request->route('id'));
        if (!$user) {
            return response()->json(['message' => 'Invalid Signature.'], Response::HTTP_FORBIDDEN);
        }
        if ($hash !== sha1($user->email)) {
            return response()->json(['message' => 'Invalid Signature.'], Response::HTTP_FORBIDDEN);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect(env('FRONTEND_URL'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        return redirect(env('FRONTEND_URL'));
    }


    /**
     * @OA\Post(
     *     path="/api/v1/email/verify/resend",
     *     summary="Resend verification email",
     *     tags={"Verification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", description="User's email address")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verification link sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Verification link sent!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Your email is already verified.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     )
     * )
     */
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->only('email'))->first();
        if ($user->hasVerifiedEmail()) {
            // Email is already verified
            return response()->json([
                'message' => 'Your email is already verified.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification link sent!',
        ], Response::HTTP_OK);
    }
}
