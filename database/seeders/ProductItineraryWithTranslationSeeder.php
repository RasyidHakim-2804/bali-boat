<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductItineraryWithTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $itineraries = [
            [
                'en' => [
                    'title' => 'Starting/pickup location',
                    'description' => 'Sanur Harbor',
                ],
                'id' => [
                    'title' => 'Lokasi mulai/penjemputan',
                    'description' => 'Pelabuhan Sanur',
                ],
            ],
            [
                'en' => [
                    'title' => 'Fastboat',
                    'description' => '(45 minutes)',
                ],
                'id' => [
                    'title' => 'Perahu cepat',
                    'description' => '(45 menit)',
                ],
            ],
            [
                'en' => [
                    'title' => 'Manta Bay',
                    'description' => 'Visit, Guided activity, Sightseeing, Snorkeling, Safety briefing, Scenic views on the way (30 minutes)',
                ],
                'id' => [
                    'title' => 'Teluk Manta',
                    'description' => 'Kunjungan, Aktivitas terpandu, Wisata, Snorkeling, Briefing keamanan, Pemandangan indah dalam perjalanan (30 menit)',
                ],
            ],
            [
                'en' => [
                    'title' => 'Pasih Uug Beach',
                    'description' => 'Pass by (5 minutes)',
                ],
                'id' => [
                    'title' => 'Pantai Pasih Uug',
                    'description' => 'Lewat saja (5 menit)',
                ],
            ],
            [
                'en' => [
                    'title' => "Angel's Billabong",
                    'description' => 'Pass by (5 minutes)',
                ],
                'id' => [
                    'title' => "Kolam Angel",
                    'description' => 'Lewat saja (5 menit)',
                ],
            ],
            [
                'en' => [
                    'title' => 'Secret stop',
                    'description' => 'Snorkeling (30 minutes)',
                ],
                'id' => [
                    'title' => 'Pemberhentian rahasia',
                    'description' => 'Snorkeling (30 menit)',
                ],
            ],
            [
                'en' => [
                    'title' => 'Nusa Penida',
                    'description' => 'Lunch (1 hour)',
                ],
                'id' => [
                    'title' => 'Nusa Penida',
                    'description' => 'Makan siang (1 jam)',
                ],
            ],
            [
                'en' => [
                    'title' => 'Car',
                    'description' => '(45 minutes)',
                ],
                'id' => [
                    'title' => 'Mobil',
                    'description' => '(45 menit)',
                ],
            ],
            [
                'en' => [
                    'title' => 'Kelingking Beach',
                    'description' => 'Visit, Guided activity, Sightseeing, Walk, Scenic views on the way (20 minutes)',
                ],
                'id' => [
                    'title' => 'Pantai Kelingking',
                    'description' => 'Kunjungan, Aktivitas terpandu, Wisata, Jalan kaki, Pemandangan indah dalam perjalanan (20 menit)',
                ],
            ],
            [
                'en' => [
                    'title' => 'Car',
                    'description' => '(45 minutes)',
                ],
                'id' => [
                    'title' => 'Mobil',
                    'description' => '(45 menit)',
                ],
            ],
            [
                'en' => [
                    'title' => 'Mangrove Point',
                    'description' => 'Snorkeling (40 minutes)',
                ],
                'id' => [
                    'title' => 'Titik Mangrove',
                    'description' => 'Snorkeling (40 menit)',
                ],
            ],
            [
                'en' => [
                    'title' => 'Fastboat',
                    'description' => '(45 minutes)',
                ],
                'id' => [
                    'title' => 'Perahu cepat',
                    'description' => '(45 menit)',
                ],
            ],
            [
                'en' => [
                    'title' => 'Sanur',
                    'description' => 'Tour package completed',
                ],
                'id' => [
                    'title' => 'Sanur',
                    'description' => 'Paket tur selesai',
                ],
            ],
        ];

        $id = 1;

        foreach ($itineraries as $itinerary) {
            
            DB::table('product_itineraries')->insert([
                'id'         => $id,
                'image'      => 'https://picsum.photos/50/50',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($itinerary as $lang => $array) {
                foreach ($array as $field => $value) {
                    DB::table('itinerary_translations')->insert([
                        'field'         => $field,
                        'translation'   => $value,
                        'language'      => $lang,
                        'itinerary_id'  => $id,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            $id++;
        }
        
    }
}
