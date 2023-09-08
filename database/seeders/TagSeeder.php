<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all previous records
        DB::table('tags')->delete();

        // Manual creation example
        Tag::create(['name' => 'Technology']);

        // Or, if you're using factories
        \App\Models\Tag::factory(10)->create();
    }
}
