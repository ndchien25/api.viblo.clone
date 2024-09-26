<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="The unique identifier of the user", example=1),
 *     @OA\Property(property="username", type="string", description="The username of the user", example="john_doe"),
 *     @OA\Property(property="display_name", type="string", description="The display name of the user", example="John Doe"),
 *     @OA\Property(property="fullname", type="string", description="The full name of the user", example="Johnathan Doe"),
 *     @OA\Property(property="email", type="string", format="email", description="The email address of the user", example="john.doe@example.com"),
 *     @OA\Property(property="avatar", type="string", description="The URL of the user's avatar", example="https://example.com/avatar/john_doe.jpg"),
 *     @OA\Property(property="role_id", type="integer", description="The role ID of the user", example=2),
 *     @OA\Property(property="address", type="string", nullable=true, description="The address of the user", example="123 Main St, Springfield"),
 *     @OA\Property(property="phone", type="string", nullable=true, description="The phone number of the user", example="+1234567890"),
 *     @OA\Property(property="university", type="string", nullable=true, description="The university of the user", example="Springfield University"),
 *     @OA\Property(property="followers_count", type="integer", description="The number of followers the user has", example=100),
 *     @OA\Property(property="following_count", type="integer", description="The number of users the user is following", example=50),
 *     @OA\Property(property="total_view", type="integer", description="The total view count for the user's content", example=1500),
 *     @OA\Property(property="bookmark_count", type="integer", description="The number of bookmarks the user has", example=25)
 * )
 */
class UserResource extends JsonResource
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
            'username' => $this->username,
            'display_name' => $this->display_name,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'role_id' => $this->role_id,
            'address' => $this->address,
            'phone' => $this->phone,
            'university' => $this->university,
            'followers_count' => $this->followers_count,
            'following_count' => $this->following_count,
            'total_view' => $this->total_view,
            'bookmark_count' => $this->bookmark_count,
        ];
    }
}
