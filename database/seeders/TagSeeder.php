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
                'type'          => 'private-boat',
                'translations'  => [
                    'en' => 'Private tour',
                    'id' => 'Tur pribadi',
                ]
            ],
            [
                'type'          => 'private-boat',
                'translations'  => [
                    'en' => '8 - 10 hours',
                    'id' => '8 - 10 jam',
                ]
            ],
            [
                'type'          => 'private-boat',
                'translations'  => [
                    'en' => 'Free lunch',
                    'id' => 'Makan siang gratis',
                ]
            ],
            [
                'type'          => 'private-boat',
                'translations'  => [
                    'en' => 'Pickup Add-on Available',
                    'id' => 'Tambahan penjemputan tersedia',
                ]
            ],
        ];

        $id = 1;

        foreach ($tags as $tag) {
            DB::table('tags')->insert([
                'id'         => $id,
                'type'       => $tag['type'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($tag['translations'] as $lang => $value) {
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
