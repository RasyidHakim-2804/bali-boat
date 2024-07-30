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
            'email'             => 'info@admin.com',
            'email_verified_at' => now(),
            'password'          => bcrypt('admin#2022!'),
            'is_active'         => 1
        ]);

        \DB::table('admins')->insert([
            'first_name'    => 'Second',
            'last_name'     => 'Administration',
            'uuid'          => Str::random(12),
            'email'         => 'second@admin.com',
            'password'      => bcrypt('admin#2022!'),
        ]);

        \DB::table('admins')->insert([
            'first_name'    => 'Third',
            'last_name'     => 'Administration',
            'uuid'          => Str::random(12),
            'email'         => 'third@admin.com',
            'password'      => bcrypt('admin#2022!'),
        ]);
    }
}
