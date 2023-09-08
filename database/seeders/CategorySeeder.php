<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if table is empty
        if (Category::all()->count() == 0) {
            // Create some example categories
            Category::create(['name' => 'Technology']);
            Category::create(['name' => 'Travel']);
            Category::create(['name' => 'Food']);
            Category::create(['name' => 'Lifestyle']);
            Category::create(['name' => 'Business']);
            Category::create(['name' => 'Entertainment']);

            $this->command->info('Categories seeded!');
        } else {
            $this->command->warn('Categories table is not empty. Skipping seeding.');
        }
    }
}
