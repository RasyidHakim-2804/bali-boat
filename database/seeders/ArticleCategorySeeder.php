<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ArticleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('article_categories')->insert([
            'id'            => 1,
            'name'          => 'Media',
            'slug'          => Str::slug('Media'),
            'created_at'    => now(),
        ]);

        DB::table('article_categories')->insert([
            'id'            => 2,
            'name'          => 'Event',
            'slug'          => Str::slug('Event'),
            'created_at'    => now(),
        ]);

        DB::table('article_categories')->insert([
            'id'            => 3,
            'name'          => 'Article',
            'slug'          => Str::slug('Article'),
            'created_at'    => now(),
        ]);
    }
}
