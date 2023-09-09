<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all previous records
        DB::table('users')->delete();

        // Create a single user manually (optional)
        User::create(
            [
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // replace 'password' with your desired password
            'remember_token' => \Str::random(10),
            'current_team_id' => null,
            'profile_photo_path' => null,
            ]
        );

        // Create 50 random users using the UserFactory
        User::factory(50)->create();
    }
}
