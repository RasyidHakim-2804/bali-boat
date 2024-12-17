<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PivotActivityExcludeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($activity_id = 1; $activity_id < 4; $activity_id++) {
            
            for ($exclude_id = 1; $exclude_id <= 2; $exclude_id++) { 

                DB::table('pivot_activity_excludes')->insert([
                    'activity_id'     => $activity_id,
                    'exclude_id'          => $exclude_id,
                    'position'            => $exclude_id,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        }
    }
}
