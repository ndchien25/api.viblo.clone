<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Repositories\User\IUserRepository;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyEmailController extends Controller
{
    public function __construct(protected IUserRepository $userRepository) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $user = $this->userRepository->findOrFail($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            return redirect(env('FRONTEND_URL'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        return redirect(env('FRONTEND_URL'));
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = $this->userRepository->findByField("email", $request->only('email'));
        if (!$user->hasVerifiedEmail()) {
            // Send verification email
            $user->sendEmailVerificationNotification();
            return response()->json([
                'message' => 'Verification link sent!',
            ], Response::HTTP_OK);
        }

        // Email is already verified
        return response()->json([
            'message' => 'Your email is already verified.',
        ], Response::HTTP_OK);
    }
}