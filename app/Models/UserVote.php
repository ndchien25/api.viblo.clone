<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVote extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id', 'vote'];

    /**
     * Define the relationship between a vote and a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship between a vote and a post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
