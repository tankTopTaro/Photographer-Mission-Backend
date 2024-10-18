<?php

namespace Database\Seeders;

use App\Models\Photobooth;
use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhotoboothSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing venues
        $venues = Venue::all();

        // Check if there are venues to assign to photobooths
        if ($venues->isEmpty()) {
            $this->command->warn('No venues available. Please seed venues first.'); 
            return;
        }

        // Create a photoboth for each venue
        foreach ($venues as $venue) {
            Photobooth::create([
                'venue_id' => $venue->id,   // Assign the venue ID
                'name' => $venue->name . ' Photobooth', // Unique name for each photobooth
            ]);
        }
    }
}
