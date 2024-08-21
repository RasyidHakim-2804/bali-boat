<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivateBoatVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($private_boat_id = 1; $private_boat_id < 5; $private_boat_id++) { 
            DB::table('private_boat_variants')->insert([
                'private_boat_id'      => $private_boat_id,
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
