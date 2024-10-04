<?php

namespace App\Events;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class NewCommentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Comment $comment;
    public string $postId;

    /**
     * Create a new event instance.
     *
     * @param  Comment  $comment
     * @param  Post  $post
     * @return void
     */
    public function __construct(Comment $comment, string $postId)
    {
        $this->comment = $comment;
        $this->postId = $postId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('post.comment.'.$this->postId);
    }

    public function broadcastAs()
    {
        return 'post.comment.created';
    }

    /** 
     * Dữ liệu để broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->comment->id,
            'user_id' => $this->comment->user_id,
            'post_id' => $this->postId,
            'type' => $this->comment->type,
            'content' => $this->comment->content,
            'parent_id' => $this->comment->parent_id,
            'row_count' => $this->comment->row_count,
            'user' => $this->comment->user,
            'updated_at' => $this->comment->updated_at->toDateTimeString(),
        ];
    }
}
