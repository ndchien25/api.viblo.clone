<?php

namespace Tests\Feature;

use App\Events\NewCommentCreated;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommentTest extends TestCase
{

    #[Test]
    public function testAuthenticatedUserCanCreateComment()
    {
        $this->actingAs($this->user);

        $payload = [
            'post_id' => $this->post->id,
            'type' => 'post',
            'content' => 'This is a test comment.',
            'parent_id' => null,
        ];

        Event::fake();
        
        $response = $this->postJson("/api/v1/posts/{$this->post->id}/comments", $payload);

        Event::assertDispatched(NewCommentCreated::class, function ($event) {
            return $event->comment->post_id === $this->post->id && $event->comment->user_id === $this->user->id;
        });
        
        $response->assertCreated()
            ->assertJsonStructure([
                'comment' => [
                    'id',
                    'post_id',
                    'user_id',
                    'type',
                    'content',
                    'parent_id',
                    'created_at',
                    'updated_at',
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
            ]);

        $this->assertDatabaseHas('comments', [
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'content' => 'This is a test comment.',
            'parent_id' => null,
        ]);
    }

    #[Test]
    public function testUnauthenticatedUserCannotCreateComment()
    {
        $payload = [
            'post_id' => $this->post->id,
            'type' => 'comment',
            'content' => 'This is a test comment.',
            'parent_id' => null,
        ];

        $response = $this->postJson("/api/v1/posts/{$this->post->id}/comments", $payload);

        $response->assertUnauthorized()->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function testCreatingCommentWithInvalidDataReturnsErrors()
    {
        $this->actingAs($this->user);

        $payload = [
            'post_id' => 9999999,
            'type' => 'comment',
            // 'content' is missing
            'parent_id' => null,
        ];

        $response = $this->postJson("/api/v1/posts/{$this->post->id}/comments", $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['content', 'type', 'post_id']);
    }

    #[Test]
    public function testCanRetrieveParentCommentsWithPagination()
    {
        Comment::factory()->count(5)->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'parent_id' => null,
        ]);

        $response = $this->getJson("/api/v1/posts/{$this->post->id}/comments?page=1&per_page=5");

        $response->assertOk()
            ->assertJsonStructure([
                'comments' => [
                    '*' => [
                        'id',
                        'post_id',
                        'user_id',
                        'type',
                        'content',
                        'parent_id',
                        'created_at',
                        'updated_at',
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
                'current_page',
                'total_pages',
            ])
            ->assertJsonCount(5, 'comments');
    }

    #[Test]
    public function testRetrievingParentCommentsWithInvalidPostIdReturnsErrors()
    {
        // Arrange
        $this->actingAs($this->user);

        // Act
        $response = $this->getJson("/api/v1/posts/99999/comments");

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['postId']);
    }

    public function test_can_retrieve_child_comments_with_pagination()
    {
        $this->actingAs($this->user);

        // Create a parent comment
        $parentComment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'parent_id' => null,
        ]);

        // Create 3 child comments
        Comment::factory()->count(3)->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'parent_id' => $parentComment->id,
        ]);

        $response = $this->getJson("/api/v1/posts/{$this->post->id}/comments/{$parentComment->id}/replies?page=1&per_page=3");

        $response->assertOk()
            ->assertJsonStructure([
                'comments' => [
                    '*' => [
                        'id',
                        'post_id',
                        'user_id',
                        'type',
                        'content',
                        'parent_id',
                        'created_at',
                        'updated_at',
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
                'current_page',
                'total_pages',
            ])
            ->assertJsonCount(3, 'comments');
    }

    #[Test]
    public function testRetrievingChildCommentsWithInvalidIdsReturnsErrors()
    {
        $this->actingAs($this->user);

        $response1 = $this->getJson("/api/v1/posts/99999/comments/1/replies");

        $response1->assertUnprocessable()
            ->assertJsonValidationErrors(['postId']);

        $response2 = $this->getJson("/api/v1/posts/{$this->post->id}/comments/99999/replies");

        $response2->assertUnprocessable()
            ->assertJsonValidationErrors(['parentId']);
    }

    #[Test]
    public function testAuthenticatedUserCanUpdateOwnComment()
    {
        $this->actingAs($this->user);

        $comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'content' => 'Original content',
        ]);

        $payload = [
            'content' => 'Updated content',
        ];

        $response = $this->putJson("/api/v1/posts/{$this->post->id}/comments/{$comment->id}", $payload);

        $response->assertOk()
            ->assertJsonStructure([
                'comment' => [
                    'id',
                    'post_id',
                    'user_id',
                    'type',
                    'content',
                    'parent_id',
                    'created_at',
                    'updated_at',
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
            ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated content',
        ]);
    }

    #[Test]
    public function test_authenticated_user_cannot_update_others_comment()
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $otherUser->id,
            'content' => 'Original content',
        ]);

        $payload = [
            'content' => 'Updated content',
        ];

        $this->actingAs($this->user);
        $response = $this->putJson("/api/v1/posts/{$this->post->id}/comments/{$comment->id}", $payload);

        // Assert
        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthorized action.',
            ]);

        // Ensure content was not updated
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Original content',
        ]);
    }

    #[Test]
    public function testUnauthenticatedUserCannotUpdateComment()
    {
        $comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'content' => 'Original content',
        ]);

        $payload = [
            'content' => 'Updated content',
        ];

        $response = $this->putJson("/api/v1/posts/{$this->post->id}/comments/{$comment->id}", $payload);

        $response->assertUnauthorized();
    }

    public function testUpdatingCommentWithInvalidDataReturnsErrors()
    {
        $this->actingAs($this->user);
        $comment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'content' => 'Original content',
        ]);

        $payload = [
            // 'content' is missing
        ];

        // Act
        $response = $this->putJson("/api/v1/posts/{$this->post->id}/comments/{$comment->id}", $payload);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    #[Test]
    public function testUpdatingNonExistingCommentReturns404()
    {
        // Arrange
        $this->actingAs($this->user);
        $nonExistingCommentId = 99999;

        $payload = [
            'content' => 'Updated content',
        ];

        // Act
        $response = $this->putJson("/api/v1/posts/{$this->post->id}/comments/{$nonExistingCommentId}", $payload);

        // Assert
        $response->assertNotFound()
            ->assertJson([
                'message' => 'Comment not found.',
            ]);
    }

    #[Test]
    public function testCanRetrieveChildComments()
    {
        $parentComment = Comment::factory()->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'content' => 'Parent comment',
            'parent_id' => null,
        ]);

        // Create 3 child comments
        Comment::factory()->count(3)->create([
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'content' => 'Child comment',
            'parent_id' => $parentComment->id,
        ]);

        // Act
        $response = $this->getJson("/api/v1/posts/{$this->post->id}/comments/{$parentComment->id}/replies?page=1&per_page=3");

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'comments' => [
                    '*' => [
                        'id',
                        'post_id',
                        'user_id',
                        'type',
                        'content',
                        'parent_id',
                        'created_at',
                        'updated_at',
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
                'current_page',
                'total_pages',
            ])
            ->assertJsonCount(3, 'comments');
    }

    #[Test]
    public function testRetrievingChildCommentsWithInvalidParentIdReturnsErrors()
    {
        $response = $this->getJson("/api/v1/posts/{$this->post->id}/comments/99999/replies");

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['parentId']);
    }
}
