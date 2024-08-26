<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                'en' => 'Private tour',
                'id' => 'Tur pribadi',
            ],
            [
                'en' => '8 - 10 hours',
                'id' => '8 - 10 jam',
            ],
            [
                'en' => 'Free lunch',
                'id' => 'Makan siang gratis',
            ],
            [
                'en' => 'Pickup Add-on Available',
                'id' => 'Tambahan penjemputan tersedia',
            ],
        ];

        $id = 1;

        foreach ($tags as $tag) {
            DB::table('tags')->insert([
                'id'         => $id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($tag as $lang => $value) {
                DB::table('tag_translations')->insert([
                    'field'         => 'name',
                    'translation'   => $value,
                    'language'      => $lang,
                    'tag_id'        => $id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            $id++;
        }
    }
}
