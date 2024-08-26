<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFollowing extends Model
{
    use HasFactory;

    protected $table = 'user_followings'; 

    protected $fillable = [
        'follower_id',
        'followed_id',
    ];

    public $timestamps = false;
}
