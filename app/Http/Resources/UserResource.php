<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
