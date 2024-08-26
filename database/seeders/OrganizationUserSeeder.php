<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Organization;
use Faker\Factory as Faker;

class OrganizationUserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $users = User::all();
        $organizations = Organization::all();

        foreach ($users as $user) {
            $organization = $organizations->random();
            DB::table('organization_users')->insert([
                'organ_id' => $organization->id,
                'user_id' => $user->id,
                'role' => $faker->randomElement(['admin', 'member']),
                'total_post' => $faker->numberBetween(0, 100),
                'total_member' => $faker->numberBetween(0, 100),
                'total_view' => $faker->numberBetween(0, 10000),
                'joined_at' => now(),
            ]);
        }
    }
}
