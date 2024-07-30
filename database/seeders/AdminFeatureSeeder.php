<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        \DB::table('admin_features')->insert(
            [
                'name'          => 'orders',
                'created_at'    => now(),
                'updated_at'    => now()
            ]
        );

        \DB::table('admin_features')->insert(
            [
                'name'          => 'articles',
                'created_at'    => now(),
                'updated_at'    => now()
            ]
        );

        \DB::table('admin_features')->insert(
            [
                'name'          => 'users',
                'created_at'    => now(),
                'updated_at'    => now()
            ]
        );

        \DB::table('admin_features')->insert(
            [
                'name'          => 'admins',
                'created_at'    => now(),
                'updated_at'    => now()
            ]
        );

        \DB::table('admin_features')->insert(
            [
                'name'          => 'contacts',
                'created_at'    => now(),
                'updated_at'    => now()
            ]
        );

        \DB::table('admin_features')->insert(
            [
                'name'          => 'products',
                'created_at'    => now(),
                'updated_at'    => now()
            ]
        );

        \DB::table('admin_features')->insert(
            [
                'name'          => 'additionals',
                'created_at'    => now(),
                'updated_at'    => now()
            ]
        );

        \DB::table('admin_features')->insert(
            [
                'name'          => 'locations',
                'created_at'    => now(),
                'updated_at'    => now()
            ]
        );
    }
}
