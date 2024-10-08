<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="PostResource", 
 *      type="object", 
 *      title="Post Resource", 
 *      description="Post Resource model",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="user_id", type="integer", example=1),
 *      @OA\Property(property="serie_id", type="integer", example=1), 
 *      @OA\Property(property="organ_id", type="integer", example=2), 
 *      @OA\Property(property="title", type="string", example="Introduction to Laravel"), 
 *      @OA\Property(property="slug", type="string", example="introduction-to-laravel"), 
 *      @OA\Property(property="status", type="string", example="published"), 
 *      @OA\Property(property="schedule_at", type="string", format="date-time", example="2024-09-26T10:30:00Z"), 
 *      @OA\Property(property="publish_at", type="string", format="date-time", example="2024-09-26T12:00:00Z"), 
 *      @OA\Property(property="view_count", type="integer", example=1234), 
 *      @OA\Property(property="vote", type="integer", example=5), 
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-25T10:00:00Z"), 
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-25T12:00:00Z"), 
 *      @OA\Property(property="comment_count", type="integer", example=10), 
 *      @OA\Property(property="tags", ref="#/components/schemas/TagResource"),  
 *      @OA\Property(property="user", ref="#/components/schemas/UserResource"))
 */

class PostResource extends JsonResource
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
            'user_id' => $this->user_id,
            'serie_id' => $this->serie_id,
            'organ_id' => $this->organ_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'view_count' => $this->view_count,
            'vote' => $this->vote,
            'created_at' => $this->created_at,
            'comment_count' => $this->comments_count,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
