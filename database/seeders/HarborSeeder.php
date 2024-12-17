<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HarborSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Gilitrawangan', 'Sanur', 'Nusa Penida', 'Lombok'
        ];

        $id = 1;

        foreach ($names as $name) {
            DB::table('harbors')->insert([
                'id'            => $id,
                'name'          => $name,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $id++;
        }
    }
}
