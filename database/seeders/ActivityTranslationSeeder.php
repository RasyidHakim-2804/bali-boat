<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($activity_id = 1; $activity_id < 5; $activity_id++) { 
            foreach (Language::code() as $lang) {
                DB::table('activity_translations')->insert([
                    'activity_id'       => $activity_id,
                    'field'         => 'highlight',
                    'translation'   => "Kelingking beach view Angel’s Billabong Broken beach Crystal bay beach",
                    'language'      => $lang,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                DB::table('activity_translations')->insert([
                    'activity_id'       => $activity_id,
                    'field'         => 'description',
                    'translation'   => "Board a boat just for your activity in Sanur and sail over blue waters to reach Nusa Penida Island. The first snorkeling spot is Manta Bay, or Manta Point (depending on the weather). Pull on the snorkeling gear and swim with the majestic manta rays. Next, cruise to Secret Bay and Wall Bay and stop to swim and snorkel in the crystal clear water of the area. As you snorkel, capture videos and photos of your underwater adventure with the provided Go Pro. At the end of the snorkeling, it's time to relax ! Lunch break on the Island in a restaurant with a wonderful view and an infinity pool. After lunch, drive to Kelingking Beach, an iconic location on Nusa Penida known for its breathtaking views and pristine white sand. Spend some time here and enjoy the view. Afterward, enjoy a 1km long drift snorkeling in front of Nusa Lembongan island. One of our favorite spot, called corals and turtles garden. Returning to Sanur by private boat.",
                    'language'      => $lang,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}
