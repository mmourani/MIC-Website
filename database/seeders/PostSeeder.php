<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use App\Models\User;
use Faker\Factory as Faker;  // Import the Faker library

class PostSeeder extends Seeder
{
    public function run()
    {
        // Clear out the existing posts
        DB::table('posts')->truncate();  // Use truncate for better performance

        $faker = Faker::create();  // Initialize Faker

        // Fetch all user IDs for better randomization
        $userIds = User::all()->pluck('id')->toArray();

        // Create 50 random posts
        foreach (range(1, 50) as $index) {
            // Select a random user ID
            $userId = $faker->randomElement($userIds);

            // Create the post
            Post::create(
                [
                'title'   => $faker->sentence,  // Generates a random title
                'content' => $faker->text(2000),  // Generates a text up to 2000 characters long
                'user_id' => $userId
                ]
            );
        }
    }
}

