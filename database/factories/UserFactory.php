<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName,
            'display_name' => fake()->name,
            'fullname' => fake()->name,
            'email' => fake()->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // or Hash::make('password')
            'avatar' => fake()->imageUrl,
            'role_id' => Role::inRandomOrder()->first()->id,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
