<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'username' => $this->faker->unique()->userName,
            'display_name' => $this->faker->name,
            'fullname' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // or Hash::make('password')
            'avatar' => $this->faker->imageUrl,
            'role_id' => Role::inRandomOrder()->first()->id,
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'university' => $this->faker->word,
            'followers_count' => $this->faker->numberBetween(0, 1000),
            'following_count' => $this->faker->numberBetween(0, 1000),
            'total_view' => $this->faker->numberBetween(0, 10000),
            'bookmark_count' => $this->faker->numberBetween(0, 100),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
