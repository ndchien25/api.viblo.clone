<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use App\Models\Serie;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Bookmark;
use App\Models\UserFollowing;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles
        Role::factory()->create([
            'role_name' => 'admin'
        ]);
        Role::factory()->create([
            'role_name' => 'moderator'
        ]);
        Role::factory()->create([
            'role_name' => 'regular_user'
        ]);

        // Seed users
        User::factory(20)->create();

        // Seed organizations
        Organization::factory(5)->create();

        // Seed series
        Serie::factory(5)->create();

        // Seed tags
        Tag::factory(10)->create();

        // Seed posts
        Post::factory(20)->create();

        // Seed comments
        Comment::factory(30)->create()->each(function ($comment) {
            // Seed replies to some comments
            if (rand(0, 1)) {
                Comment::factory()->withParent($comment->id)->create([
                    'user_id' => User::inRandomOrder()->first()->id,
                    'post_id' => $comment->post_id,
                ]);
            }
        });

        // Seed bookmarks
        $existingBookmarks = collect();
        for ($i = 0; $i < 15; $i++) {
            do {
                $bookmark = Bookmark::factory()->make();
            } while ($existingBookmarks->contains(function ($item) use ($bookmark) {
                return $item->user_id == $bookmark->user_id && $item->post_id == $bookmark->post_id;
            }));

            $existingBookmarks->push($bookmark);
            $bookmark->save();
        }

        // Seed activity logs
        ActivityLog::factory(25)->create();

        $this->call([
            UserFollowingSeeder::class,
            OrganizationUserSeeder::class,
            PostTagSeeder::class
        ]);
    }
}
