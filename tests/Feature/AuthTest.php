<?php

namespace Tests\Feature;

use App\Models\User;
use DB;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Str;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function invalidInputLogin()
    {
        $response = $this->postJson('/api/v1/login', [
            'email_or_username' => Str::random(55),
            'password' => '123',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors([
            'email_or_username',
            'password'
        ]);
    }

    #[Test]
    public function unauthorizedForInvalidLogin()
    {
        $response = $this->postJson('/api/v1/login', [
            'email_or_username' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertUnauthorized();
        $this->assertEquals('Wrong email/username or password!', $response->json('message'));
    }

    #[Test]
    public function errorForUnverifiedEmailOnLogin()
    {
        User::factory()->create([
            'email' => 'nona.upton@example.org',
            'password' => bcrypt('password'),
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email_or_username' => 'nona.upton@example.org',
            'password' => 'password',
        ]);

        $response->assertUnauthorized()->assertJson([
            'error' => true,
            'message' => 'Email not verified!',
            'verified' => false,
        ]);
    }

    #[Test]
    public function loginUserWithEmail()
    {
        $response = $this->postJson('/api/v1/login', [
            'email_or_username' => $this->user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()->assertJson([
            'error' => false,
            'message' => 'Login successful!',
            'verified' => true,
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'display_name' => $this->user->display_name,
                'fullname' => $this->user->fullname,
                'email' => $this->user->email,
                'avatar' => $this->user->avatar,
                'role_id' => $this->user->role_id,
                'address' => $this->user->address,
                'phone' => $this->user->phone,
                'university' => $this->user->university,
                'followers_count' => $this->user->followers_count,
                'following_count' => $this->user->following_count,
                'total_view' => $this->user->total_view,
                'bookmark_count' => $this->user->bookmark_count,
            ]
        ]);
    }

    #[Test]
    public function invalidRegistration()
    {
        $response = $this->postJson('/api/v1/register', [
            'email' => 'invalid-email',
            'display_name' => '',
            'username' => '',
            'password' => '',
            'c_password' => '321',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['email', 'username', 'password', 'display_name', 'c_password']);
    }

    #[Test]
    public function registerUser()
    {
        $data = [
            'display_name' => 'testuser',
            'username' => 'user',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'c_password' => 'password123',
        ];

        Event::fake();

        $response = $this->postJson('/api/v1/register', $data);

        $response->assertOk()->assertJson([
            'username' => 'user',
            'email' => 'testuser@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'user',
            'email' => 'testuser@example.com',
        ]);

        Event::assertDispatched(Registered::class);
    }

    #[Test]
    public function invalidEmailOnResetLink()
    {
        $response = $this->postJson('/api/v1/forgot-password', [
            'email' => 'invalid-email',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function sendResetLinkEmail()
    {
        $response = $this->postJson('/api/v1/forgot-password', [
            'email' => $this->user->email,
        ]);

        $response->assertOk();
        $this->assertEquals('We have emailed your password reset link.', $response->json('message'));

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $this->user->email,
        ]);
    }

    #[Test]
    public function invalidInputResetPassword()
    {
        $response = $this->postJson('/api/v1/reset-password', [
            'email' => 'invalid-email',
            'password' => 'newpassword122',
            'c_password' => 'newpassword123',
            'token' => 'some-invalid-token',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['email', 'c_password']);
    }

    #[Test]
    public function errorForInvalidToken()
    {
        $data = [
            'email' => $this->user->email,
            'password' => 'newpassword123',
            'c_password' => 'newpassword123',
            'token' => 'invalid-token',
        ];

        Password::shouldReceive('reset')->once()->andReturn(Password::INVALID_TOKEN);

        $response = $this->postJson('/api/v1/reset-password', $data);

        $response->assertBadRequest()->assertJson([
            'message' => __('This password reset token is invalid.'),
        ]);
    }

    #[Test]
    public function errorForInvalidUser()
    {
        $data = [
            'email' => 'nonexistentuser@example.com',
            'password' => 'newpassword123',
            'c_password' => 'newpassword123',
            'token' => 'some-valid-token',
        ];

        $response = $this->postJson('/api/v1/reset-password', $data);

        $response->assertUnprocessable()->assertJsonValidationErrors(['email']);
    }
    #[Test]
    public function resetPasswordSuccessfully()
    {
        $plainToken = Str::random(60);

        DB::table('password_reset_tokens')->insert([
            'email' => $this->user->email,
            'token' => Hash::make($plainToken),
            'created_at' => now(),
        ]);

        $data = [
            'email' => $this->user->email,
            'password' => 'newpassword123',
            'c_password' => 'newpassword123',
            'token' => $plainToken,
        ];

        $response = $this->postJson('/api/v1/reset-password', $data);

        $response->assertOk()->assertJson([
            'message' => __('Your password has been reset.'),
        ]);

        $this->user->refresh();
        $this->assertTrue(password_verify('newpassword123', $this->user->password));
    }

    #[Test]
    public function logoutInvalidUser()
    {
        $response = $this->postJson('/api/v1/logout');

        $response->assertUnauthorized()->assertJson([
            'message' => 'Unauthenticated.',
        ]);

        $this->assertFalse(Auth::check());
    }

    public function userCanLogoutSuccessfully()
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/v1/logout');

        $response->assertAccepted()->assertJson([
            'error' => false,
            'message' => 'Successfully logged out!',
        ]);

        $this->assertFalse(Auth::check());
    }

    public function unauthenticatedUserCannotGetTheirDetails()
    {
        $response = $this->getJson('/api/v1/me');

        $response->assertUnauthorized()->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function authenticatedUserCanGetTheirDetails()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/v1/me');

        $response->assertOk()->assertJson([
                'authenticated' => true,
                'user' => [
                    'id' => $this->user->id,
                    'username' => $this->user->username,
                    'display_name' => $this->user->display_name,
                    'fullname' => $this->user->fullname,
                    'email' => $this->user->email,
                    'avatar' => $this->user->avatar,
                    'role_id' => $this->user->role_id,
                    'address' => $this->user->address,
                    'phone' => $this->user->phone,
                    'university' => $this->user->university,
                    'followers_count' => $this->user->followers_count,
                    'following_count' => $this->user->following_count,
                    'total_view' => $this->user->total_view,
                    'bookmark_count' => $this->user->bookmark_count,
                ],
            ]);
    }
}
