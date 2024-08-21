<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityIncludeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($activity_id = 1; $activity_id < 5; $activity_id++) {
            for ($position = 1; $position <= rand(2,5); $position++) { 
                foreach (Language::code() as $lang) {
                    DB::table('activity_includes')->insert([
                        'position'    => $position,
                        'value'       => 'include ' . $lang . ' position ' . $position,
                        'activity_id'     => $activity_id,
                        'language'    => $lang,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }   
            }
        }
    }
}
