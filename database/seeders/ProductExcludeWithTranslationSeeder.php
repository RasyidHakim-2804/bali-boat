<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductExcludeWithTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $excludes = [
            [
                'en' => 'Transport from a hotel in Bali to Sanur port.',
                'id' => 'Transportasi dari hotel di Bali ke pelabuhan Sanur.',
            ],
            [
                'en' => 'Ticket to any private property of selfie spot in Kelingking & Paluang cliff area.',
                'id' => 'Tiket ke properti pribadi area spot selfie di Kelingking & tebing Paluang.',
            ],
        ];

        
        $id = 1;

        foreach ($excludes as $exclude) {
            
            DB::table('product_excludes')->insert([
                'id'         => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($exclude as $lang => $value) {
                DB::table('exclude_translations')->insert([
                    'field'         => 'description',
                    'translation'   => $value,
                    'language'      => $lang,
                    'exclude_id'    => $id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            $id++;
        }
        
    }
}
