<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Arr;

class CategoryPostSeeder extends Seeder
{
    public function run()
    {
        // Delete all previous records
        DB::table('category_post')->truncate();

        // Fetch all post IDs
        $postIds = Post::all()->pluck('id')->toArray();

        // Fetch all category IDs
        $categoryIds = Category::all()->pluck('id')->toArray();

        // Verify that both posts and categories exist
        if (empty($postIds) || empty($categoryIds)) {
            $this->command->info('Posts or Categories not available. Skipping seeding for category_post table.');
            return;
        }

        foreach ($postIds as $postId) {
            // Verify the array is not empty
            if (!empty($categoryIds)) {
                // Choose a random number of category IDs, but limit it to the number of available categories
                $countOfRandomCategoryIds = rand(1, min(3, count($categoryIds)));
                $randomCategoryIds = Arr::random($categoryIds, $countOfRandomCategoryIds);
                foreach ($randomCategoryIds as $categoryId) {
                    DB::table('category_post')->insert(
                        [
                        'post_id' => $postId,
                        'category_id' => $categoryId,
                        ]
                    );
                }
            } else {
                $this->command->info("No categories available for post ID: $postId. Skipping...");
            }
        }
    }
}
