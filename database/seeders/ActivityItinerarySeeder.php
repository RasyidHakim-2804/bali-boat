<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityItinerarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($activity_id = 1; $activity_id < 5; $activity_id++) {
            foreach (Language::code() as $lang) {
                DB::table('activity_itineraries')->insert([
                    'type'        => 'start',
                    'title'       => 'Starting/pickup location',
                    'description' => 'Sanur Harbor',
                    'position'    => 1,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('activity_itineraries')->insert([
                    'type'        => 'boat',
                    'title'       => 'Fastboat',
                    'description' => '(45 minutes)',
                    'position'    => 2,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('activity_itineraries')->insert([
                    'type'        => 'location',
                    'title'       => 'Manta Bay',
                    'description' => 'Visit, Guided activity, Sightseeing, Snorkeling, Safety briefing, Scenic views on the way (30 minutes)',
                    'position'    => 3,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('activity_itineraries')->insert([
                    'type'        => 'location',
                    'title'       => 'Pasih Uug Beach',
                    'description' => 'Pass by (5 minutes)',
                    'position'    => 4,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('activity_itineraries')->insert([
                    'type'        => 'location',
                    'title'       => "Angel's Billabong",
                    'description' => 'Pass by (5 minutes)',
                    'position'    => 5,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('activity_itineraries')->insert([
                    'type'        => 'location',
                    'title'       => 'Secret stop',
                    'description' => 'Snorkeling (30 minutes)',
                    'position'    => 6,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('activity_itineraries')->insert([
                    'type'        => 'location',
                    'title'       => 'Nusa Penida',
                    'description' => 'Lunch (1 hour)',
                    'position'    => 7,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('activity_itineraries')->insert([
                    'type'        => 'car',
                    'title'       => 'Car',
                    'description' => '(45 minutes)',
                    'position'    => 8,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('activity_itineraries')->insert([
                    'type'        => 'location',
                    'title'       => 'Kelingking Beach',
                    'description' => 'Visit, Guided activity, Sightseeing, Walk, Scenic views on the way (20 minutes)',
                    'position'    => 9,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('activity_itineraries')->insert([
                    'type'        => 'car',
                    'title'       => 'Car',
                    'description' => '(45 minutes)',
                    'position'    => 10,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('activity_itineraries')->insert([
                    'type'        => 'location',
                    'title'       => 'Mangrove Point',
                    'description' => 'Snorkeling (40 minutes)',
                    'position'    => 11,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('activity_itineraries')->insert([
                    'type'        => 'boat',
                    'title'       => 'Fastboat',
                    'description' => '(45 minutes)',
                    'position'    => 12,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                DB::table('activity_itineraries')->insert([
                    'type'        => 'location',
                    'title'       => 'Sanur',
                    'description' => 'Tour package completed',
                    'position'    => 13,
                    'activity_id'     => $activity_id,
                    'language'    => $lang,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}
