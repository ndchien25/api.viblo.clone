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
        $tags = Tag::pluck('id')->toArray();
        $idspost = Post::pluck('id')->toArray();
        $data = [];

        foreach($idspost as $id) {
            $randomTags = array_rand(array_flip($tags), rand(3, 7));

            foreach($randomTags as $tagId) {
                $data[] = [
                    'post_id' => $id,
                    'tag_id' => $tagId,
                ];
            }
        } 

        $chunks = array_chunk($data, 5000);

        foreach ($chunks as $chunk) {
            DB::table('post_tags')->insert($chunk);
        }
    }
}
