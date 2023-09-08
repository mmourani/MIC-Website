<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Tag;

class PostTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all previous records
        DB::table('post_tag')->truncate();

        // Fetch all post IDs
        $postIds = Post::all()->pluck('id')->toArray();

        // Fetch all tag IDs
        $tagIds = Tag::all()->pluck('id')->toArray();

        // Loop through each post and associate random tags
        foreach ($postIds as $postId) {
            $randomTagIds = array_rand($tagIds, 2); // Choose 2 random tags for this example
            $tagsToAttach = [$tagIds[$randomTagIds[0]], $tagIds[$randomTagIds[1]]];
            Post::find($postId)->tags()->attach($tagsToAttach);
        }
    }
}
