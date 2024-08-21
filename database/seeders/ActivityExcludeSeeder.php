<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityExcludeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($tour_id = 1; $tour_id < 5; $tour_id++) {
            for ($position = 1; $position <= rand(2,5); $position++) { 
                foreach (Language::code() as $lang) {
                    DB::table('activity_excludes')->insert([
                        'position'    => $position,
                        'value'       => 'exclude ' . $lang . ' position ' . $position,
                        'activity_id'     => $tour_id,
                        'language'    => $lang,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }   
            }
        }
    }
}
