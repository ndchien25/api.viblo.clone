<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="StoreCommentRequest",
 *    type="object",
 *    required={"post_id", "type", "content"},
 *    @OA\Property(property="post_id", type="integer", description="ID of the post", example=1),
 *    @OA\Property(property="type", type="string", enum={"post", "question"}, description="Type of comment", example="post"),
 *    @OA\Property(property="content", type="string", description="Content of the comment", example="This is a sample comment."),
 *    @OA\Property(property="parent_id", type="integer", nullable=true, description="ID of the parent comment (if it's a reply)", example=null)
 * )
 */
class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'post_id' => 'required|exists:posts,id',
            'type' => 'required|in:post,question',
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ];
    }
}
