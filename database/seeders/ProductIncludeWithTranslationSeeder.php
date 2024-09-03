<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductIncludeWithTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $includes = [
            [
                'type'          => 'activity',
                'translations'  => [
                    'en' => 'Roundtrip fast boat ticket to Nusa Penida.',
                    'id' => 'Tiket fast boat pulang-pergi ke Nusa Penida.'
                ],
            ],
            [
                'type'          => 'activity',
                'translations'  => [
                    'en' => 'Private car for day tour in Nusa Penida.',
                    'id' => 'Mobil pribadi untuk tur sehari di Nusa Penida.'
                ],
            ],
            [
                'type'          => 'activity',
                'translations'  => [
                    'en' => 'Driver as guide & photographer.',
                    'id' => 'Sopir sebagai pemandu & fotografer.'
                ],
            ],
            [
                'type'          => 'activity',
                'translations'  => [
                    'en' => 'Lunch at local restaurant.',
                    'id' => 'Makan siang di restoran lokal.'
                ],
            ],
            [
                'type'          => 'activity',
                'translations'  => [
                    'en' => 'Mineral water.',
                    'id' => 'Air mineral.'
                ],
            ],
            [
                'type'          => 'activity',
                'translations'  => [
                    'en' => 'Entrance ticket to all mentioned tourist objects.',
                    'id' => 'Tiket masuk ke semua objek wisata yang disebutkan.'
                ],
            ],
            [
                'type'          => 'activity',
                'translations'  => [
                    'en' => 'Admission fee at Nusa Penida harbor.',
                    'id' => 'Biaya masuk di pelabuhan Nusa Penida.'
                ],
            ],
            [
                'type'         => 'private-boat',
                'translations' => [
                    'en' => "1 captain & 1 crew, fuel, cruising permit.",
                    'id' => "1 kapten & 1 kru, bahan bakar, izin berlayar."
                ]
            ],
            [
                'type'         => 'private-boat',
                'translations' => [
                    'en' => "Seasafe tracking, satellite phone.",
                    'id' => "Pelacak Seasafe, telepon satelit."
                ]
            ],
            [
                'type'         => 'private-boat',
                'translations' => [
                    'en' => "Fishing tool, snorkeling gear, table.",
                    'id' => "Peralatan memancing, perlengkapan snorkeling, meja."
                ]
            ],
            [
                'type'         => 'private-boat',
                'translations' => [
                    'en' => "Insurance, life jacket, life ring, pool towel.",
                    'id' => "Asuransi, jaket pelampung, pelampung, handuk kolam."
                ]
            ],
            [
                'type'         => 'private-boat',
                'translations' => [
                    'en' => "Mineral water, soft drink, ice box.",
                    'id' => "Air mineral, minuman ringan, kotak es."
                ]
            ]
        ];
        
        $id = 1;

        foreach ($includes as $include) {
            
            DB::table('product_includes')->insert([
                'id'         => $id,
                'type'       => $include['type'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($include['translations'] as $lang => $value) {
                DB::table('include_translations')->insert([
                    'field'         => 'description',
                    'translation'   => $value,
                    'language'      => $lang,
                    'include_id'    => $id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            $id++;
        }
        
    }
}
