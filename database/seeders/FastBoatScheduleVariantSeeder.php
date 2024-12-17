<?php

namespace Database\Seeders;

use App\Models\FastBoatSchedule;
use App\Models\FastBoatScheduleVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FastBoatScheduleVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = FastBoatSchedule::all();

        foreach ($schedules as $schedule) {
            foreach (FastBoatScheduleVariant::PASSENGER_TYPE as $passenger) {
                foreach (FastBoatScheduleVariant::AGE_TYPE as $age) {
                    
                    DB::table('fast_boat_schedule_variants')->insert([
                        'fast_boat_schedule_id' => $schedule->id,
                        'passenger_type'   => $passenger,
                        'age_type'         => $age,
                        'price'            => fake()->randomFloat(2,20000, 30000),
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }
        }
    }
}
