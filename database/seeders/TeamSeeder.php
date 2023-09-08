<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\User;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if table is empty
        if (Team::all()->count() == 0) {

            // Fetch all user IDs
            $userIds = User::all()->pluck('id')->toArray();

            // Verify that users exist
            if (!empty($userIds)) {

                // Array to hold some example team names
                $teamNames = ['Development', 'Marketing', 'Finance', 'Research'];

                foreach ($userIds as $userId) {

                    // Assign a personal team
                    Team::create([
                        'user_id' => $userId,
                        'name' => 'Personal-' . $userId,
                        'personal_team' => true
                    ]);

                    // Create additional teams for this user
                    $numTeams = rand(1, 3); // Random number of teams between 1 and 3
                    for ($i = 0; $i < $numTeams; $i++) {
                        $teamName = $teamNames[array_rand($teamNames)] . '-' . $userId . '-' . $i;
                        Team::create([
                            'user_id' => $userId,
                            'name' => $teamName,
                            'personal_team' => false
                        ]);
                    }
                }

                $this->command->info('Teams seeded!');

            } else {
                $this->command->warn('Users table is empty. Skipping team seeding.');
            }

        } else {
            $this->command->warn('Teams table is not empty. Skipping seeding.');
        }
    }
}
