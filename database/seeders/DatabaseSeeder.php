<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PostSeeder::class,
            PostTagSeeder::class,
            UserFollowingSeeder::class,
            OrganizationUserSeeder::class,
        ]);
    }
}
