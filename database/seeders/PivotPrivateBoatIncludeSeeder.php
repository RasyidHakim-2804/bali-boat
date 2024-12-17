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
            
            DB::table('pivot_private_boat_includes')->insert([
                'private_boat_id'     => $private_boat_id,
                'include_id'          => 8,
                'position'            => 1,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            DB::table('pivot_private_boat_includes')->insert([
                'private_boat_id'     => $private_boat_id,
                'include_id'          => 9,
                'position'            => 2,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            DB::table('pivot_private_boat_includes')->insert([
                'private_boat_id'     => $private_boat_id,
                'include_id'          => 10,
                'position'            => 3,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            DB::table('pivot_private_boat_includes')->insert([
                'private_boat_id'     => $private_boat_id,
                'include_id'          => 11,
                'position'            => 4,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            DB::table('pivot_private_boat_includes')->insert([
                'private_boat_id'     => $private_boat_id,
                'include_id'          => 12,
                'position'            => 5,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }
    }
}
