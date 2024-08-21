<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('languages')->insert([
            'id'    => 1,
            'code'  => 'en',
            'language'  => 'English',
            'created_at'    => now(),
        ]);

        DB::table('languages')->insert([
            'id'    => 2,
            'code'  => 'id',
            'language'  => 'Indonesia',
            'created_at'    => now(),
        ]);
    }
}
