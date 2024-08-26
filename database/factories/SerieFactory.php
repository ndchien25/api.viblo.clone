<?php

namespace Database\Factories;

use App\Models\Serie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Serie>
 */
class SerieFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Serie::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->text,
            'slug' => $this->faker->slug,
            'user_id' => User::inRandomOrder()->first()->id,
            'post_ids' => [], // For PostgreSQL array type; adjust for other DBs
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
