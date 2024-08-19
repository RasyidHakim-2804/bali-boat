<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('admins')->insert([
            'first_name'        => 'Master',
            'last_name'         => 'Administration',
            'uuid'              => Str::random(12),
            'email'             => 'info@baliboat.id',
            'email_verified_at' => now(),
            'password'          => bcrypt('Bestboat@55677gs!'),
            'is_active'         => 1
        ]);
    }
}
