<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FastBoatScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([1,2,3] as $boat_id) {
            DB::table('fast_boat_schedules')->insert([
                'fast_boat_id'               => $boat_id,
                'departure_harbor_id'   => 1,
                'arrival_harbor_id'     => 2,
                'departure_time'        => '07:00',
                'arrival_time'          => '07:30',
                'active'                => 1,
            ]);

            DB::table('fast_boat_schedules')->insert([
                'fast_boat_id'               => $boat_id,
                'departure_harbor_id'   => 2,
                'arrival_harbor_id'     => 3,
                'departure_time'        => '07:30',
                'arrival_time'          => '08:00',
                'active'                => 1,
            ]);

            DB::table('fast_boat_schedules')->insert([
                'fast_boat_id'               => $boat_id,
                'departure_harbor_id'   => 3,
                'arrival_harbor_id'     => 4,
                'departure_time'        => '08:00',
                'arrival_time'          => '08:30',
                'active'                => 1,
            ]);
        }
    }
}
