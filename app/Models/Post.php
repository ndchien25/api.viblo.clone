<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'serie_id',
        'organ_id',
        'title',
        'content',
        'slug',
        'status',
        'schedule_at',
        'publish_at',
        'view_count',
        'vote',
    ];
    /**
     * Get the post that owns the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Define the many-to-many relationship with Tag
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    /**
     * Get all votes associated with the post.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(UserVote::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
