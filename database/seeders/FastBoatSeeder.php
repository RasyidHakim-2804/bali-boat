<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FastBoatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fast_boats')->insert([
            'id'            => 1,
            'name'          => 'Wijaya Buyuk Fast Boat',
            'slug'          => Str::slug('Wijaya Buyuk Fast Boat'),
            'image'         => 'https://source.unsplash.com/random/?Banner&Skincare',
            'is_active'     => 1,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('fast_boats')->insert([
            'id'            => 2,
            'name'          => 'Wanderlust Cruise',
            'slug'          => Str::slug('Wanderlust Cruise'),
            'image'         => 'https://source.unsplash.com/random/?Banner&Skincare',
            'is_active'     => 1,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('fast_boats')->insert([
            'id'            => 3,
            'name'          => 'Starfish Fast Cruise',
            'slug'          => Str::slug('Starfish Fast Cruise'),
            'image'         => 'https://source.unsplash.com/random/?Banner&Skincare',
            'is_active'     => 1,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}
