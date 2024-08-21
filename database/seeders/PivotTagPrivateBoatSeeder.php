<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PivotTagPrivateBoatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($private_boat_id = 1; $private_boat_id <= 4; $private_boat_id++) { 
            DB::table('pivot_private_boat_tags')->insert([
                'private_boat_id'    => $private_boat_id,
                'tag_id'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('pivot_private_boat_tags')->insert([
                'private_boat_id'    => $private_boat_id,
                'tag_id'     => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('pivot_private_boat_tags')->insert([
                'private_boat_id'    => $private_boat_id,
                'tag_id'     => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('pivot_private_boat_tags')->insert([
                'private_boat_id'    => $private_boat_id,
                'tag_id'     => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
