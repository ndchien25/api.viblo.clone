<?php

namespace App\Http\Services;

use App\Models\Post;
use App\Models\UserVote;

class VoteService
{
    /**
     * Upvote or downvote a post, or restore a vote if the same button is clicked again.
     *
     * @param string $postId
     * @param int $userId
     * @param string $voteType up , down, none for vote
     * @return array
     */
    public function vote(string $postId, int $userId, string $voteType): array
    {
        $post = Post::findOrFail($postId);
        $userVote = UserVote::where('user_id', $userId)->where('post_id', $postId)->first();

        // error if userVote none and vote type is none
        if (!$userVote && $voteType === 'none') {
            return [
                'error' => true,
                'message' => 'No vote to cancel.'
            ];
        }

        // Handle upvote or downvote when user hasn't voted yet
        if (!$userVote && $voteType !== 'none') {
            $post->increment('vote', $voteType === 'up' ? 1 : -1);

            $post->votes()->create([
                'user_id' => $userId,
                'vote' => $voteType
            ]);

            return [
                'error' => false,
            ];
        }

        // cancel vote
        if ($userVote && $voteType === 'none') {
            $post->increment('vote', $userVote->vote === 'up' ? -1 : 1);
            $userVote->delete();

            return [
                'error' => false,
            ];
        }

        // if have userVote and vote change up or down
        if ($userVote && $voteType !== 'none') {
            if ($userVote->vote === $voteType) {
                return [
                    'error' => true,
                    'message' => $voteType === 'up' ? 'You already upvoted this post.' : 'You already downvoted this post.'
                ];
            }

            // If user is switching their vote
            if ($voteType === 'up' && $userVote->vote === 'down') {
                $post->increment('vote', 2); // Change from downvote to upvote
            } elseif ($voteType === 'down' && $userVote->vote === 'up') {
                $post->decrement('vote', 2); // Change from upvote to downvote
            }

            // Update the user's vote
            $userVote->update([
                'vote' => $voteType
            ]);
            return [
                'error' => false,
            ];
        }
        return [
            'error' => true,
            'message' => 'Invalid vote type provided.'
        ];
    }
}
