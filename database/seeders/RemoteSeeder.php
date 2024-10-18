<?php

namespace Database\Seeders;

use App\Models\Remote;
use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RemoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing venues
        $venues = Venue::all();

        $venueRemoteCount = []; // Array to keep track of the number of remotes per venue

        // Check if there are venues to assign to remotes
        if ($venues->isEmpty()) {
            $this->command->info('No venues available. Please seed venues first.'); 
            return;
        }

        // Initialize the count of remotes per venue
        foreach ($venues as $venue) {
            $venueRemoteCount[$venue->id] = 0;
        }

        // Create 15 remotes with varying venues
        for ($i = 1; $i <= 15; $i++) {
            do {
                $randomVenue = $venues->random();   // randomly select a venue
            } while ($venueRemoteCount[$randomVenue->id] >= 3); // Ensure no more than 3 remotes per venue
        
            // Create the remote for the selected venue
            Remote::create([ 'venue_id' => $randomVenue->id ]);

            // Increment the count of remotes per venue
            $venueRemoteCount[$randomVenue->id]++;
        }
    }
}
