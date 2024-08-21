<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'Nusa Penida',
            'Kelingking Cliff',
            'Gilitrawangan'
        ];

        foreach ($locations as $location) {
            DB::table('locations')->insert([
                'name'       => $location,
                'created_at' => now(),
                'updated_at' =>now()
            ]);
        }
    }
}
