<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PrivateBoatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $private_boats = [
            'Private Yacht Sea Horse II',
            'Day Trip with Trigger Yacht',
            'Private Boat Trip Sony Lee',
            'Private Boat Trip Queen Ruizi 10'
        ];

        $id = 1;

        foreach ($private_boats as $private_boat) {
            DB::table('private_boats')->insert([
                'id'                => $id,
                'name'              => $private_boat,
                'slug'              => Str::slug($private_boat),
                'pickup_harbor_id'  => rand(1,4),
                'rating'            => 5,
                'is_active'         => 1,
                'max_person'        => 10,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $id++;
        }
    }
}
