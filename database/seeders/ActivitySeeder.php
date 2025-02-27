<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            'West Nusa Penida Tour',
            'East Nusa Penida Tour',
            'North Nusa Penida Tour',
            'South Nusa Penida Tour'
        ];

        $id = 1;

        foreach ($activities as $activity) {
            DB::table('activities')->insert([
                'id'                  => $id,
                'name'                => $activity,
                'slug'                => Str::slug($activity),
                'location_id'         => rand(1,3),
                'pickup_available'    => 1,
                'max_person'          => rand(5,9),
                'is_active'           => 1,
                'rating'              => 5,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            $id++;
        }
    }
}
