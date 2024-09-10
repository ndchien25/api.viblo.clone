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
            'slug'    => Str::slug($payload['title']) . '-' . $uniqueString,
            'user_id' => Auth::user()->id
        ]);
        if ($post) {
            $tagIds = array_column($payload['tags'], 'id');

            $post->tags()->sync($tagIds);
        }
        return $post ?? $post;
    }

    public function getPostBySlug(string $slug = '')
    {
        $post = Post::with('tags')->where('slug', $slug)->firstOrFail();
        return $post;
    }
}
