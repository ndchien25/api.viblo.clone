<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Http\Resources\UserResource;
use PHPUnit\Framework\Attributes\Test;

class UserTest extends TestCase
{   
    protected function createAdminUser()
    {
        return User::factory()->create([
            'role_id' => 1,
            'password' => Hash::make('password123'),
        ]);
    }
    #[Test]
    public function it_returns_403_for_unauthenticated_user()
    {
        $response = $this->getJson('/api/v1/admin/users');

        $response->assertStatus(403);

        $this->assertEquals('Unauthorized', $response->json('message'));
    }

    #[Test]
    public function it_returns_403_for_non_admin_user()
    {
        $nonAdminUser = User::factory()->create([
            'role_id' => 2,
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($nonAdminUser)->getJson('/api/v1/admin/users');

        $response->assertStatus(403);

        $this->assertEquals('Unauthorized', $response->json('message'));
    }

    #[Test]
    public function it_returns_422_for_invalid_requests()
    {
        $adminUser = $this->createAdminUser();

        $this->actingAs($adminUser);

        $response = $this->getJson('/api/v1/admin/users?page=invalid');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    }

    #[Test]
    public function it_can_get_a_list_of_users()
    {
        // Create 15 regular users
        User::factory()->count(15)->create();

        // Create an admin user
        $adminUser = $this->createAdminUser();

        // Act as the admin user
        $response = $this->actingAs($adminUser)->getJson('/api/v1/admin/users');

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'username',
                        'display_name',
                        'fullname',
                        'email',
                        'avatar',
                        'role_id',
                        'address',
                        'phone',
                        'university',
                        'followers_count',
                        'following_count',
                        'total_view',
                        'bookmark_count',
                    ]
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links' => [
                        '*' => [
                            'url',
                            'label',
                            'active',
                        ]
                    ],
                    'per_page',
                    'to',
                    'total'
                ],
            ]);

        $this->assertCount(10, $response->json('data'));

        $expectedUsers = User::take(10)->get();

        $expectedUserData = UserResource::collection($expectedUsers)->response()->getData(true)['data'];

        $this->assertEquals($expectedUserData, $response->json('data'));
    }
}