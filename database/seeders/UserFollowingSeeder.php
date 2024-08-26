<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserFollowing;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserFollowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Retrieve existing user IDs
        $userIds = User::pluck('id')->toArray();

        $existingPairs = [];

        for ($i = 0; $i < 20; $i++) {
            do {
                $followerId = $faker->randomElement($userIds);
                $followedId = $faker->randomElement($userIds);
            } while ($followerId === $followedId || in_array([$followerId, $followedId], $existingPairs, true));

            $existingPairs[] = [$followerId, $followedId];

            UserFollowing::updateOrCreate(
                ['follower_id' => $followerId, 'followed_id' => $followedId],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
