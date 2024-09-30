<?php

namespace Tests\Feature;

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailVerifiedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user->email_verified_at = null;
        $this->user->save();
    }

    #[Test]
    public function testVerificationEmailThrottlesRequests()
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1($this->user->email)]
        );
        for ($i = 0; $i < 7; $i++) {
            $response = $this->getJson($signedUrl);

            if ($i === 6) {
                $response->assertTooManyRequests();
                $response->assertJson(['message' => "Too Many Attempts."]);
            }
        }
    }

    #[Test]
    public function testVerificationFailsWithInvalidSignature()
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1($this->user->email)]
        );

        $tamperedUrl = preg_replace('/signature=([^&]+)/', 'signature=1', $signedUrl);

        $response = $this->getJson($tamperedUrl);

        $response->assertForbidden();
        $this->assertEquals('Invalid signature.', $response->json('message'));
        $this->user->refresh();
        $this->assertNull($this->user->email_verified_at);
    }

    #[Test]
    public function testVerificationFailsWithInvalidHash()
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1('invalidhash')]
        );
        $response = $this->getJson($signedUrl);
        $response->assertForbidden();

        $this->user->refresh();
        $this->assertNull($this->user->email_verified_at);
    }

    #[Test]
    public function testVerifiesEmailSuccessfully()
    {
        Event::fake();

        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1($this->user->email)]
        );

        $response = $this->getJson($signedUrl);

        $this->user->refresh();
        $this->assertNotNull($this->user->email_verified_at);

        $response->assertRedirect(env('FRONTEND_URL'));

        Event::assertDispatched(Verified::class);
    }

    #[Test]
    public function testRedirectsIfEmailAlreadyVerified()
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1($this->user->email)]
        );

        $response = $this->getJson($signedUrl);

        $response->assertRedirect(env('FRONTEND_URL'));
    }

    #[Test]
    public function testVerificationFailsWhenUserNotFound()
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => 999999, 'hash' => sha1('nonexistent@example.com')]
        );

        $response = $this->getJson($signedUrl);

        $response->assertForbidden();
    }

    #[Test]
    public function testResendVerificationEmailThrottlesRequests()
    {
        $this->user->refresh();
        for ($i = 0; $i < 7; $i++) {
            $response = $this->postJson(route('verification.send'), [
                'email' => $this->user->email,
            ]);

            if ($i === 6) {
                $response->assertTooManyRequests();
                $this->assertEquals('Too Many Attempts.', $response->json('message'));
            }
        }
    }
    #[Test]
    public function testResendsVerificationEmailSuccessfully()
    {
        Notification::fake();

        $response = $this->postJson(route('verification.send'), [
            'email' => $this->user->email,
        ]);

        $response->assertOk()->assertJson([
            'message' => 'Verification link sent!',
        ]);

        Notification::assertSentTo($this->user, \Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    #[Test]
    public function testResendVerificationEmailIfEmailAlreadyVerified()
    {
        $this->user->email_verified_at = now();
        $this->user->save(); 
        $response = $this->postJson(route('verification.send'), [
            'email' => $this->user->email,
        ]);

        $response->assertBadRequest()->assertJson([
            'message' => 'Your email is already verified.',
        ]);

        Notification::assertNothingSent();
    }

    #[Test]
    public function testResendVerificationEmailIfUserNotFound()
    {
        $response = $this->postJson(route('verification.send'), [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['email']);
    }
}
