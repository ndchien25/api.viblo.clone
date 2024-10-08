<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        for($i = 0; $i < 10000; $i++) {
            $data[] = [
                'username' => 'user'.$i,
                'display_name' => 'asdasdas',
                'email' => 'chien'.$i.'@gmail.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        $chunks = array_chunk($data, 5000);

        foreach ($chunks as $chunk) {
            User::insert($chunk);
        }
    }
}
