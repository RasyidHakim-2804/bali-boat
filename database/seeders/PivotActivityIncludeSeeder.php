<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PivotActivityIncludeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($activity_id = 1; $activity_id < 4; $activity_id++) {
            
            for ($include_id = 1; $include_id <= 7; $include_id++) { 

                DB::table('pivot_activity_includes')->insert([
                    'activity_id'     => $activity_id,
                    'include_id'          => $include_id,
                    'position'            => $include_id,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        }
    }
}
