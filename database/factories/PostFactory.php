<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Post;
use App\Models\Serie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{   
     /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userIds = User::pluck('id')->toArray();
        return [
            'user_id' => $this->faker->randomElement($userIds),
            'serie_id' => null,
            'organ_id' => null,
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'slug' => $this->faker->slug,
            'status' => $this->faker->randomElement(['private_draft', 'anyone_with_link', 'schedule', 'public']),
            'schedule_at' => $this->faker->dateTimeBetween('now', '+1 week'),
            'publish_at' => $this->faker->dateTimeBetween('now', '+1 week'),
            'view_count' => $this->faker->numberBetween(0, 1000),
            'vote' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
