<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::truncate();
        $data = [];
        $userIds = User::pluck('id')->toArray();
        for($i = 0; $i < 100000; $i++) {
            $data[] = [
                'user_id' => fake()->randomElement($userIds),
                'title' => fake()->sentence,
                'content' => fake()->paragraph,
                'slug' => fake()->slug,
                'status' => fake()->randomElement(['private_draft', 'anyone_with_link', 'schedule', 'public']),
                'view_count' => 0,
                'vote' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];
            if ($i && $i%1000===0) {
                Post::insert($data);
                $data = [];
            }
        }
        Post::insert($data);
    }
}
