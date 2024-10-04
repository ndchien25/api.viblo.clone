<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class CommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;
    public $postId;
    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Comment  $comment
     * @param  string  $postId
     * @return void
     */
    public function __construct($comment, $postId)
    {
        $this->comment = $comment;
        $this->postId = $postId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'database']; // Thêm các kênh khác nếu cần
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            "id" => $this->id,
            "type" => get_class($this),
            "created_at" => now()->toDateTimeString(),
            'read_at' => null,
            "data" => [
                'comment_id' => $this->comment->id,
                'post_id' => $this->postId,
                'content' => $this->comment->content,
                'parent_id' => $this->comment->parent_id,
                'post_slug' => $this->comment->post->slug,
                'user' => [
                    'id' => $this->comment->user->id,
                    'username' => $this->comment->user->username,
                    'display_name' => $this->comment->user->display_name
                ],
            ]
        ]);
    }

    /**
     * Get the array representation of the notification for database.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'comment_id' => $this->comment->id,
            'post_id' => $this->postId,
            'content' => $this->comment->content,
            'parent_id' => $this->comment->parent_id,
            'post_slug' => $this->comment->post->sluPlogg,
            'user' => [
                'id' => $this->comment->user->id,
                'username' => $this->comment->user->username,
                'display_name' => $this->comment->user->display_name
            ],
        ]);
    }
}
