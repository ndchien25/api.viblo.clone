<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CommentResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="The unique identifier of the comment", example=1),
 *     @OA\Property(property="post_id", type="integer", description="The ID of the associated post", example=10),
 *     @OA\Property(property="user_id", type="integer", description="The ID of the user who made the comment", example=5),
 *     @OA\Property(property="content", type="string", description="The content of the comment", example="This is a sample comment."),
 *     @OA\Property(property="type", type="string", enum={"post", "question"}, description="The type of comment", example="post"),
 *     @OA\Property(property="row_count", type="integer", description="The row count of the comment", example=0),
 *     @OA\Property(property="parent_id", type="integer", nullable=true, description="The ID of the parent comment if this is a reply", example=null),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="The date and time when the comment was created", example="2024-09-25T12:34:56"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="The date and time when the comment was last updated", example="2024-09-25T12:34:56"),
 *     @OA\Property(property="user", ref="#/components/schemas/UserResource", description="The user who made the comment")
 * )
 */
class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'content' => $this->content,
            'type' => $this->type,
            'row_count' => $this->row_count,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
