<?php

namespace App\Http\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostService extends BaseService
{
    public function createPost($payload = [])
    {
        $uniqueString = substr(bin2hex(random_bytes(4)), 0, 8);
        $post = Post::create([
            'title'   => $payload['title'],
            'content' => $payload['content'],
            'slug'    => Str::slug($payload['title']).'-'.$uniqueString,
            'user_id' => Auth::user()->id
        ]);
        
        return $post ?? $post;
    }
}
