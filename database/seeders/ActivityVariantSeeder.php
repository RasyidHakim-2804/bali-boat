<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($activity_id = 1; $activity_id < 5; $activity_id++) { 
            DB::table('activity_variants')->insert([
                'activity_id'        => $activity_id,
                'passenger_type' => null,
                'age_type'       => null,
                'is_default'     => 1,
                'price'          => '7000000',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
