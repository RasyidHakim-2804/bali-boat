<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PivotTagActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($activity_id = 1; $activity_id <= 4; $activity_id++) { 
            DB::table('pivot_activity_tags')->insert([
                'activity_id'    => $activity_id,
                'tag_id'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('pivot_activity_tags')->insert([
                'activity_id'    => $activity_id,
                'tag_id'     => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('pivot_activity_tags')->insert([
                'activity_id'    => $activity_id,
                'tag_id'     => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('pivot_activity_tags')->insert([
                'activity_id'    => $activity_id,
                'tag_id'     => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
