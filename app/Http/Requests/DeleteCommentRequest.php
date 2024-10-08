<?php

namespace App\Http\Requests;

use App\Models\Comment;
use App\Models\Post;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

class DeleteCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $postId = $this->route('postId');
        $commentId = $this->route('id');

        $post = Post::find($postId);
        if (!$post) {
            return false;
        }

        $comment = Comment::where('id', $commentId)
            ->where('post_id', $postId)
            ->first();

        if (!$comment) {
            return false;
        }

        // Check if the authenticated user is the owner of the comment
        return $comment->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        ];
    }
}
