<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use App\Models\Tag;

class PostTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::all();
        $tags = Tag::all();

        // Seed the post_tags table with random post-tag associations
        foreach ($posts as $post) {
            $tagsForPost = $tags->random(rand(1, 5)); // Randomly assign 1 to 5 tags per post

            foreach ($tagsForPost as $tag) {
                DB::table('post_tags')->insert([
                    'post_id' => $post->id,
                    'tag_id' => $tag->id,
                ]);
            }
        }
    }
}
