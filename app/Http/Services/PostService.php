<?php

namespace App\Http\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostService extends BaseService
{
    public function create($payload = [])
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

    public function getBySlug(string $slug = '')
    {
        $post = Post::with(['tags', 'user', 'comments'])->where('slug', $slug)->firstOrFail();
        $post->increment('view_count');
        if (Auth::check()) {
            $userId = Auth::id();
            $userVote = $post->votes()->where('user_id', $userId)->first();
            $hasUserVoted = $userVote ? $userVote->vote : null;
        } else {
            $hasUserVoted = null;
        }
        $commentCount = $post->comments()->count();
        return [
            'post' => $post,
            'user_vote' => $hasUserVoted,
            'comment_count' => $commentCount,
        ];
    }

    public function getNewest($page = 1, $perPage = 20)
    {
        return Post::latest()->with(['user', 'tags'])->withCount('comments')->paginate($perPage, ['*'], 'page', $page);
    }
}
