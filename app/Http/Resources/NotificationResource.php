<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="NotificationResource",
 *     type="object",
 *     title="NotificationResource",
 *     required={"id", "data", "type", "created_at"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="data", type="object", example={"message": "You have a new notification"}),
 *     @OA\Property(property="type", type="string", example="info"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-04T10:00:00Z"),
 *     @OA\Property(property="read_at", type="string", format="date-time", example="2023-10-04T12:00:00Z"),
 * )
 */
class NotificationResource extends JsonResource
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
            'data' => $this->data,
            'type' => $this->type,
            'created_at' => $this->created_at->toDateTimeString(),
            'read_at' => $this->read_at ? $this->read_at->toDateTimeString() : null,
        ];
    }
}
