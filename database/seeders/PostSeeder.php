<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use App\Models\User;
use Faker\Factory as Faker;

class PostSeeder extends Seeder
{
    public function run()
    {
        // Clear out the existing posts
        DB::table('posts')->truncate();

        $faker = Faker::create();

        // Fetch all existing user IDs
        $userIds = User::pluck('id')->toArray();

        // Ensure there are users to associate with posts
        if (count($userIds) === 0) {
            $this->command->info('No users found. Please create users before seeding posts.');
            return;
        }

        // Create 50 random posts
        foreach (range(1, 50) as $index) {
            // Select a random user ID
            $userId = $faker->randomElement($userIds);

            // Create the post
            Post::create([
                'title' => $faker->sentence,
                'content' => $faker->text(2000),
                'user_id' => $userId,
            ]);
        }
    }
}


