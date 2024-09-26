<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StorePostRequest",
 *     type="object",
 *     required={"title", "content", "tags"},
 *     @OA\Property(property="title", type="string", example="New Post"),
 *     @OA\Property(property="content", type="string", example="Post content"),
 *     @OA\Property(property="tags", type="array", @OA\Items(type="string", example="tag1"))
 * )
 */
class StorePostRequest extends FormRequest
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
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'required|array|min:1|max:5',
            'tags.*.id' => 'integer|exists:tags,id'
        ];
    }

    /**
     * Customize the error messages for validation failures.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'content.required' => 'Content is required.',
            'tags.required' => 'Please add at least one tag.',
            'tags.*.id.exists' => 'The selected tag is invalid.',
        ];
    }
}
