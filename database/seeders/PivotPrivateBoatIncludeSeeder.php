<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PivotPrivateBoatIncludeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($private_boat_id = 1; $private_boat_id < 5; $private_boat_id++) {
            
            for ($include_id = 1; $include_id <= 7; $include_id++) { 

                DB::table('pivot_private_boat_includes')->insert([
                    'private_boat_id'     => $private_boat_id,
                    'include_id'          => $include_id,
                    'position'            => $include_id,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        }
    }
}
