<?php

namespace App\Http\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentService extends BaseService
{
    /**
     * Create a new comment for a post or another comment (reply).
     *
     * @param array $payload An array containing the data for the comment creation.
     *                       The array should include:
     *                       - 'post_id' (int): The ID of the post the comment is for.
     *                       - 'type' (string): The type of the comment ('post' or 'question').
     *                       - 'content' (string): The content of the comment.
     *                       - 'parent_id' (int|null): The ID of the parent comment if this is a reply.
     * @return Comment The created comment instance.
     */
    public function createComment($payload = [])
    {
        $comment = Comment::create([...$payload, 'user_id' => Auth::id()]);
        if ($payload['parent_id']) {
            $parentComment = Comment::find($payload['parent_id']);
            if ($parentComment) {
                // Increment the row_count field for the parent comment
                $parentComment->increment('row_count');
            }
        }

        return $comment ?? $comment;
    }

    /**
     * Update an existing comment.
     * 
     * If the parent_id is changed, it adjusts the row_count for the old and new parent comments.
     *
     * @param int $commentId The ID of the comment to update.
     * @param array $payload An array containing the updated data for the comment.
     *                       The array may include:
     *                       - 'content' (string): The updated content of the comment.
     *                       - 'parent_id' (int|null): The ID of the new parent comment if being changed.
     * @return Comment The updated comment instance.
     */
    public function updateComment($commentId, $payload = [])
    {
        $comment = Comment::findOrFail($commentId);

        // Check if parent_id is changing
        if (isset($payload['parent_id']) && $payload['parent_id'] !== $comment->parent_id) {
            // Decrement row_count for old parent if exists
            if ($comment->parent_id) {
                $oldParent = Comment::find($comment->parent_id);
                if ($oldParent) {
                    $oldParent->decrement('row_count');
                }
            }

            // Increment row_count for new parent if exists
            if ($payload['parent_id']) {
                $newParent = Comment::find($payload['parent_id']);
                if ($newParent) {
                    $newParent->increment('row_count');
                }
            }
        }
    }
}
