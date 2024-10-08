<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [];
        for ($i = 0; $i < 100; $i++) {
            $name = fake()->word; // Tạo tên tag ngẫu nhiên
            $tags[] = [
                'name' => $name,
                'slug' => Str::slug($name), // Tạo slug từ tên tag
                'post_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('tags')->insert($tags);
    }
}
