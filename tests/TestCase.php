<?php

namespace Tests;

use App\Models\Post;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use Notification;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Post $post;
    protected function setUp(): void
    {
        parent::setUp();
        $this->createRoles();
        $this->createSpectificUser();
        $this->post = $this->createPost();
        $this->createPosts();
        $this->createTags();
        Notification::fake();
    }

    protected function createRoles()
    {
        Role::factory()->create(['id' => 1, 'role_name' => 'admin']);
        Role::factory()->create(['id' => 2, 'role_name' => 'moderator']);
        Role::factory()->create(['id' => 3, 'role_name' => 'regular_user']);
    }

    protected function createSpectificUser()
    {
        $this->user = User::factory()->create([
            'username' => 'testuser',
            'display_name' => 'Test User',
            'fullname' => 'Full Name',
            'email' => 'user@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
            'role_id' => 1,
            'address' => '123 Main St',
            'phone' => '1234567890',
            'university' => 'University Name',
            'followers_count' => 100,
            'following_count' => 50,
            'total_view' => 50,
            'bookmark_count' => 10,
            'password' => bcrypt('password123'),
        ]);
    }

    protected function createTags()
    {
        Tag::factory()->count(15)->create();
    }

    protected function createPosts()
    {
        Post::factory()->count(15)->create(['user_id' => $this->user->id]);
    }

    protected function createPost()
    {
        return Post::factory()->create(['user_id' => $this->user->id]);
    }
}
