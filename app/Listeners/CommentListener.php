<?php

namespace App\Listeners;

use App\Events\NewCommentCreated;
use App\Models\Post;
use App\Notifications\CommentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Notification;

class CommentListener implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewCommentCreated $event): void
    {
        $comment = $event->comment->load('post');

        $postId = $event->postId;

        $post = Post::find($postId);

        if (!$post) {
            return;
        }

        $recipients = collect();

        if ($post->user_id !== $comment->user_id) {
            $recipients->push($post->user);
        }

        if ($comment->parent_id) {
            $parentComment = $comment->parent;
            if ($parentComment && $parentComment->user_id !== $comment->user_id) {
                $recipients->push($parentComment->user);
            }
        }
        
        $recipients = $recipients->unique('id');
        Notification::send($recipients, new CommentNotification($comment, $postId));
    }
}
