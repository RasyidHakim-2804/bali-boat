<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivateBoatIncludeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($private_boat_id = 1; $private_boat_id < 5; $private_boat_id++) {
            for ($position = 1; $position <= rand(2,5); $position++) { 
                foreach (Language::code() as $lang) {
                    DB::table('private_boat_includes')->insert([
                        'position'      => $position,
                        'value'         => 'include ' . $lang . ' position ' . $position,
                        'private_boat_id'     => $private_boat_id,
                        'language'      => $lang,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }   
            }
        }
    }
}
