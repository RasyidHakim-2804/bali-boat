<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivateBoatTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($private_boat_id = 1; $private_boat_id < 5; $private_boat_id++) { 
            foreach (Language::code() as $lang) {
                DB::table('private_boat_translations')->insert([
                    'private_boat_id'     => $private_boat_id,
                    'field'         => 'highlight',
                    'translation'   => "Cool Yacht RideImagine  cruising on a fancy yacht, feeling the wind and enjoying the beautiful  views of the Bali Sea. You'll travel in a really nice boat, making the  journey as exciting as the destination. Snorkeling Fun at Manta Bay, Crystal Bay and Gamat Bay.We'll  take you to the best and most iconic underwater panorama around Nusa  Penida sea, a place with super clear water and lots of cool fish  including a chance to swim with Manta rays. Put on your snorkel gear and  dive into the water to see amazing coral reefs and colorful fish. Fun Fishing & Barbecue Delight.Engage  in lively bottom fishing, trying your luck to catch some fish on board.  Enjoy the excitement of fishing, and later, unwind at a beachside  restaurant with a barbecue feast featuring your fresh catch. Lunch with Ocean Views.Dock  at a picturesque beachside restaurant for a delectable lunch. Relish a  variety of tasty dishes with the ocean as your backdrop. Guides to Help You Out.Our  friendly guides will be there to show you around, share interesting  stuff about the local culture, and make sure you have a blast. You won't  have to worry about a thing – just enjoy the ride! Chill Sunset Cruise.Back  To end the day, we'll take you back to the harbor on a relaxing cruise,  just in time for a beautiful sunset. Reflect on all the fun you had as  the sun goes down over Bali.",
                    'language'      => $lang,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                DB::table('private_boat_translations')->insert([
                    'private_boat_id'     => $private_boat_id,
                    'field'         => 'meta',
                    'translation'   => "Get from Nusa Penida to Sanur quickly and comfortably with this speedboat transfer service. Relax while gliding over the sea on a stress-free trip with a certified crew and efficient transportation.",
                    'language'      => $lang,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}
