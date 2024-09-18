<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'status' => $this->status,
            'schedule_at' => $this->schedule_at,
            'publish_at' => $this->publish_at,
            'view_count' => $this->view_count,
            'vote' => $this->vote,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'comment_count' => $this->comments_count,
            'tags' => new TagResource($this->whenLoaded('tags')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
