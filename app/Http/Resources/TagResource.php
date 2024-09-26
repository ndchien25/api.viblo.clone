<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="TagResource", type="object", title="Tag Resource", description="Tag Resource representation", 
 * @OA\Property(property="id", type="integer", description="ID of the tag"), 
 * @OA\Property(property="name", type="string", description="Name of the tag"), 
 * @OA\Property(property="slug", type="string", description="Slug of the tag"), 
 * @OA\Property(property="post_count", type="integer", description="Number of posts associated with the tag"), 
 * @OA\Property(property="created_at", type="string", format="date-time", description="Tag creation timestamp"), 
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Tag update timestamp")
 * )
 */
class TagResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'post_count' => $this->post_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
