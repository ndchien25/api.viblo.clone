<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostTest extends TestCase
{
    #[Test]
    public function testReturnsForInvalidParameters()
    {
        $response = $this->getJson('/api/v1/posts?page=aadsad&perPage=asdasdas');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['page', 'perPage']);
    }

    #[Test]
    public function testCanGetAListOfPosts()
    {
        $response = $this->getJson('/api/v1/posts?page=1&perPage=5');
        $response->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'title',
                    'slug',
                    'view_count',
                    'vote',
                    'created_at',
                    'comment_count',
                    'tags' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'post_count',
                        ],
                    ],
                    'user' => [
                        'id',
                        'username',
                        'display_name',
                        'email',
                        'avatar',
                        'role_id',
                        'followers_count',
                        'following_count',
                        'total_view',
                        'bookmark_count',
                    ],
                ],
            ],
            'meta' => [
                'current_page',
                'last_page',
            ],
        ])
            ->assertJsonCount(5, 'data');
    }

    #[Test]
    public function testReturnsErrorForInvalidData()
    {
        $this->actingAs($this->user);

        $data = [
            'title' => '',
            'content' => '',
            'tags' => [],
        ];

        $response = $this->postJson('/api/v1/posts', $data);

        $response->assertUnprocessable()->assertJsonValidationErrors(['title', 'content', 'tags']);
    }

    #[Test]
    public function testStoresPostSuccessfully()
    {
        $this->actingAs($this->user);

        $tags = Tag::take(3)->get();
        $payload = [
            'title' => 'New Post',
            'content' => 'This is the content of the new post.',
            'tags' => $tags->toArray()
        ];

        // Gọi dịch vụ tạo bài viết
        $response = $this->postJson('/api/v1/posts', $payload);

        $this->assertDatabaseHas('posts', [
            'slug' => $response->json('slug'),
        ]);

        $response->assertStatus(201) // Kiểm tra mã trạng thái HTTP
            ->assertJson([
                'message' => 'Bài viết được tạo thành công!!',
                'slug' => $response->json('slug')
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => $payload['title'],
            'content' => $payload['content'],
        ]);

        // Kiểm tra thẻ đã được gán cho bài viết
        foreach ($tags as $tag) {
            $this->assertDatabaseHas('post_tags', [
                'post_id' => Post::where('title', $payload['title'])->first()->id,
                'tag_id' => $tag->id,
            ]);
        }
    }

    #[Test]
    public function testValidatesSlugFormat()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/v1/posts/Invalid-Slug!');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }

    #[Test]
    public function testReturnsForNonExistingSlug()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/v1/posts/non-existing-slug');

        // Kiểm tra phản hồi
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }

    #[Test]
    public function testCanGetAPostBySlug()
    {
        $this->actingAs($this->user);

        $post = Post::factory()->create([
            'title' => 'Test Post',
            'content' => 'This is the content of the test post.',
            'slug' => 'test-post-unique',
        ]);

        $response = $this->getJson("/api/v1/posts/{$post->slug}");

        $response->assertOk()->assertJsonStructure([
            'post' => [
                'id',
                'user_id',
                'serie_id',
                'organ_id',
                'title',
                'slug',
                'status',
                'schedule_at',
                'publish_at',
                'view_count',
                'vote',
                'created_at',
                'updated_at',
                'tags' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'post_count',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'user' => [
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
                ],
            ],
            'comment_count',
            'user_vote'
        ]);
    }

    
}
