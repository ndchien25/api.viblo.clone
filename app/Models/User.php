<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /** 
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'display_name',
        'username',
        'email',
        'email_verify',
        'password',
        'avatar',
        'role_id',
        'address',
        'phone_number',
        'university',
        'followers_count',
        'following_count',
        'total_view',
        'bookmark_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role associated with the user.
     */
    public function role(): HasOne
    {
        return $this->hasOne(Role::class);
    }

    /**
     * The organ that belong to the user.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_user', 'user_id', 'organ_id')
            ->withPivot('role', 'total_post', 'total_member', 'total_view', 'joined_at');
    }

    /**
     * Get the posts for the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the series for the user.
     */
    public function series(): HasMany
    {
        return $this->hasMany(Serie::class);
    }

    /**
     * Get the comments for user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'bookmarks');
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_following', 'follower_id', 'followed_id');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_following', 'followed_id', 'follower_id');
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'target');
    }
}
