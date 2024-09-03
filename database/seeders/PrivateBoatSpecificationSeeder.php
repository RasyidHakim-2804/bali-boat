<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivateBoatSpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($private_boat_id = 1; $private_boat_id < 5; $private_boat_id++) { 
            DB::table('private_boat_specifications')->insert([
                'private_boat_id'     => $private_boat_id,
                'model'         => 'Azimut 43',
                'year'          => '2017',
                'length'        => '43ft (13.16m)',
                'cabins'        => '2',
                'heads'         => '3',
                'engine'        => 'Volva Penta IPS',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
