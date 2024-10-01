<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostVoteTest extends TestCase
{
    #[Test]
    public function testRequiresAuthenticationToVote()
    {
        $this->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'up'])->assertUnauthorized();
    }

    #[Test]
    public function testReturnsErrorForNonExistingPost()
    {
        $this->actingAs($this->user)->postJson('/api/v1/posts/999999/vote', ['vote' => 'up'])
            ->assertUnprocessable()->assertJsonValidationErrors(['id']);
    }

    #[Test]
    public function testReturnsErrorForInvalidVoteType()
    {
        $this->actingAs($this->user);

        $this->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'invalid_vote_type'])
            ->assertUnprocessable()->assertJsonValidationErrors(['vote']);
    }

    #[Test]
    public function testCanUpvoteOnAPost()
    {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'up']);

        $response->assertOk()->assertJson([
            'error' => false,
        ]);

        $this->assertDatabaseHas('user_votes', [
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'vote' => 'up',
        ]);

        $this->assertEquals(1, $this->post->fresh()->vote);
    }

    public function testUserCanDownvoteAPost()
    {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'down']);

        $response->assertOk()->assertJson([
            'error' => false,
        ]);

        $this->assertDatabaseHas('user_votes', [
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
            'vote' => 'down',
        ]);

        $this->assertEquals(-1, $this->post->fresh()->vote);
    }

    #[Test]
    public function testUserCannotCancelVoteWhenNoVoteExists()
    {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'none']);

        $response->assertBadRequest()->assertJson([
            'error' => true,
            'message' => 'No vote to cancel.',
        ]);
    }
    #[Test]
    public function testUserCannotUpvoteAgain()
    {
        $this->actingAs($this->user)
            ->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'up']);

        // Người dùng cố gắng upvote lại
        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'up']);

        $response->assertBadRequest()
            ->assertJson([
                'error' => true,
                'message' => 'You already upvoted this post.',
            ]);

        $this->assertEquals(1, $this->post->fresh()->vote);
    }

    #[Test]
    public function test_user_cannot_downvote_again_when_already_downvoted()
    {
        $this->actingAs($this->user)
            ->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'down']);

        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'down']);

        $response->assertBadRequest()
            ->assertJson([
                'error' => true,
                'message' => 'You already downvoted this post.',
            ]);

        $this->assertEquals(-1, $this->post->fresh()->vote);
    }

    #[Test]
    public function testUserCanSwitchVoteFromDownToUp()
    {
        $this->actingAs($this->user)
            ->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'down']);

        // Switch to upvote
        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'up']);

        $response->assertOk()
            ->assertJson([
                'error' => false,
            ]);

        $this->assertEquals(1, $this->post->fresh()->vote);

        // Ensure the user_vote record is updated
        $this->assertDatabaseHas('user_votes', [
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
            'vote' => 'up',
        ]);
    }

    #[Test]
    public function testUserCanWwitchVoteFromUpToDown()
    {
        $this->actingAs($this->user)
            ->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'up']);

        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'down']);

        $response->assertOk()
            ->assertJson([
                'error' => false,
            ]);

        $this->assertEquals(-1, $this->post->fresh()->vote);

        $this->assertDatabaseHas('user_votes', [
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
            'vote' => 'down',
        ]);
    }

    #[Test]
    public function testUserCanCancelUpvote()
    {
        // First upvote
        $this->actingAs($this->user)
            ->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'up']);

        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'none']);

        $response->assertOk()
            ->assertJson([
                'error' => false,
            ]);

        $this->assertEquals(0, $this->post->fresh()->vote);

        // Ensure the user_vote record is deleted
        $this->assertDatabaseMissing('user_votes', [
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);
    }

    #[Test]
    public function testUserCanCancelDownvote()
    {
        // First downvote
        $this->actingAs($this->user)
            ->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'down']);

        // Cancel the vote
        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/posts/{$this->post->id}/vote", ['vote' => 'none']);

        $response->assertOk()
            ->assertJson([
                'error' => false,
            ]);

        // Ensure vote count is updated correctly
        $this->assertEquals(0, $this->post->fresh()->vote);

        // Ensure the user_vote record is deleted
        $this->assertDatabaseMissing('user_votes', [
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);
    }
}
