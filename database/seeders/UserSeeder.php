<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker      = \Faker\Factory::create();
        $firstname  = $faker->firstName();
        $lastName   = $faker->lastName();

        // for ($i=0; $i < 2 ; $i++) {

        //     \DB::table('users')->insert(
        //         [
        //             'uuid'              => Str::random(12),
        //             'first_name'        => $firstname,
        //             'last_name'         => $lastName,
        //             'email'             => $faker->email(),
        //             'email_verified_at' => now(),
        //             'password'          => bcrypt('user#2022!'),
        //             'profile_picture'   => null,
        //             'country'           => $faker->country(),
        //             'city'              => $faker->city(),
        //             'phone'             => $faker->phoneNumber(),
        //             'is_active'         => 1,
        //             'created_at'        => now(),
        //             'updated_at'        => now()
        //         ]
        //     );
        // }

        // \DB::table('users')->insert(
        //     [
        //         'uuid'              => Str::random(12),
        //         'first_name'        => $firstname,
        //         'last_name'         => $lastName,
        //         'email'             => 'dsdfd2342@email.com',
        //         'email_verified_at' => now(),
        //         'password'          => null,
        //         'profile_picture'   => null,
        //         'country'           => $faker->country(),
        //         'city'              => $faker->city(),
        //         'phone'             => $faker->phoneNumber(),
        //         'is_active'         => 1,
        //         'created_at'        => now(),
        //         'updated_at'        => now()
        //     ]
        // );

        DB::table('users')->insert(
            [
                'uuid'              => Str::random(12),
                'first_name'        => $firstname,
                'last_name'         => $lastName,
                'email'             => 'test@email.com',
                'phone'             => fake()->phoneNumber(),
                'email_verified_at' => now(),
                'password'          => bcrypt('user#2022!'),
                'is_active'         => 1,
                'created_at'        => now(),
                'updated_at'        => now()
            ]
        );
    }
}
