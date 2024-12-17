<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PivotActivityItinerarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($activity_id = 1; $activity_id < 4; $activity_id++) {
            
            for ($itinerary_id = 1; $itinerary_id <= 13; $itinerary_id++) { 

                DB::table('pivot_activity_itineraries')->insert([
                    'activity_id'     => $activity_id,
                    'itinerary_id'    => $itinerary_id,
                    'position'        => $itinerary_id,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }
    }
}
