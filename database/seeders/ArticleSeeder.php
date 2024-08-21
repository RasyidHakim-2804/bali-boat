<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        for ($i=0; $i < 10 ; $i++) {
            $title = $faker->sentence(2);
            $slug  = strtolower(str_replace(" ", "-", $title));

            DB::table('articles')->insert(
                [
                    'title'             => $title,
                    'slug'              => $slug,
                    'body'              => '<p>' . $faker->sentence(100) . '</p>',
                    'category'          => $faker->sentence(1),
                    'meta'              => $faker->sentence(5),
                    'total_view'        => $faker->numberBetween(0, 100),
                    'category_id'       => 1,
                    'image'             => 'https://source.unsplash.com/random/?Banner&Skincare',
                    'created_at'        => now(),
                    'updated_at'        => now()
                ]
            );
        }
    }
}
