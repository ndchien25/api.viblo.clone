<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserFollowing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserFollowing>
 */
class UserFollowingFactory extends Factory
{   
    protected $model = UserFollowing::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        do {
            $followerId = User::inRandomOrder()->first()->id;
            $followedId = User::inRandomOrder()->first()->id;
        } while ($followerId == $followedId);

        return [
            'follower_id' => $followerId,
            'followed_id' => $followedId,
            'created_at'  => now(),
            'updated_at'  => now()
        ];
    }
}
