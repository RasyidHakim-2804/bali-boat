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
                'en' => 'Roundtrip fast boat ticket to Nusa Penida.',
                'id' => 'Tiket fast boat pulang-pergi ke Nusa Penida.'
            ],
            [
                'en' => 'Private car for day tour in Nusa Penida.',
                'id' => 'Mobil pribadi untuk tur sehari di Nusa Penida.'
            ],
            [
                'en' => 'Driver as guide & photographer.',
                'id' => 'Sopir sebagai pemandu & fotografer.'
            ],
            [
                'en' => 'Lunch at local restaurant.',
                'id' => 'Makan siang di restoran lokal.'
            ],
            [
                'en' => 'Mineral water.',
                'id' => 'Air mineral.'
            ],
            [
                'en' => 'Entrance ticket to all mentioned tourist objects.',
                'id' => 'Tiket masuk ke semua objek wisata yang disebutkan.'
            ],
            [
                'en' => 'Admission fee at Nusa Penida harbor.',
                'id' => 'Biaya masuk di pelabuhan Nusa Penida.'
            ]
        ];
        
        $id = 1;

        foreach ($includes as $include) {
            
            DB::table('product_includes')->insert([
                'id'         => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($include as $lang => $value) {
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
