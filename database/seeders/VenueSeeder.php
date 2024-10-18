<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $venues = [
            ['name' => 'Illusion Theatre'],
            ['name' => 'VR Zone'],
            ['name' => '3D Art Gallery'],
            ['name' => 'Escape Rooms'],
            ['name' => 'Interactive Exhibits'],
            ['name' => 'Café'],
            ['name' => 'Gift Shop'],
        ];

        Venue::insert($venues);
    }
}
