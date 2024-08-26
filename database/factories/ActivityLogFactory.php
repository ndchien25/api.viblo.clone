<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'action_type' => $this->faker->randomElement(['follow', 'vote', 'comment', 'post']),
            'target_type' => $this->faker->randomElement(['user', 'serie','post', 'comment', 'organization']),
            'target_id' => function (array $attributes) {
                switch ($attributes['target_type']) {
                    case 'post':
                        return Post::factory()->create()->id;
                    case 'comment':
                        return Comment::factory()->create()->id;
                    case 'tag':
                        return Tag::factory()->create()->id;
                }
            },
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
