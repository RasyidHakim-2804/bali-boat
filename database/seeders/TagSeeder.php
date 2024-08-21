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
        $names = [
            'Private tour', '8 - 10 hours', 'Free lunch', 'Pickup Add-on Available'
        ];

        $id = 1;
        foreach ($names as $name) {
            DB::table('tags')->insert([
                'id'         => $id,
                'name'       => $name,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $id++;
        }
    }
}
