<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('promos')->insert([
            'code'              => '10PERCENT',
            'type'              => 'percentage',
            'uuid'              => Str::random(12),
            'duration'          => 'repeat',
            'amount'            => 10,
            'start_at'          => '2024-08-30',
            'expired_at'        => '2024-09-30',
            'is_active'         => 1
        ]);

        DB::table('promos')->insert([
            'code'              => '20RIBU',
            'type'              => 'fixed',
            'uuid'              => Str::random(12),
            'duration'          => 'repeat',
            'amount'            => 20,
            'start_at'          => '2024-08-30',
            'expired_at'        => '2024-09-30',
            'is_active'         => 1
        ]);

        DB::table('promos')->insert([
            'code'              => '30RIBU',
            'type'              => 'fixed',
            'uuid'              => Str::random(12),
            'duration'          => 'once',
            'amount'            => 20,
            'start_at'          => '2024-08-30',
            'expired_at'        => '2024-09-30',
            'is_active'         => 1
        ]);
    }
}
