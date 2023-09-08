<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Faker\Factory as Faker;  // Import the Faker library

class CommentSeeder extends Seeder
{
    public function run()
    {
        // Clear out the existing comments
        DB::table('comments')->truncate(); // Use truncate instead of delete for better performance

        $faker = Faker::create(); // Initialize the Faker instance

        // Fetch all user and post IDs for better randomization
        $userIds = User::all()->pluck('id')->toArray();
        $postIds = Post::all()->pluck('id')->toArray();

        // Create 100 random comments
        foreach (range(1, 100) as $index) {
            // Select random user and post IDs
            $userId = $faker->randomElement($userIds);
            $postId = $faker->randomElement($postIds);

            // Create the comment
            Comment::create([
                'content' => $faker->paragraph, // Generates a realistic paragraph
                'user_id' => $userId,
                'post_id' => $postId
            ]);
        }
    }
}
