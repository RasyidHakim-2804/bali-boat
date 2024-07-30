<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InitialSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        Artisan::call('migrate:fresh');
        Artisan::call('passport:keys');
        Artisan::call('key:generate');
        Artisan::call('passport:client --personal --name="Blue Season Personal Access Client"');
        Artisan::call('storage:link');

        // ADMIN
        Artisan::call('db:seed --class=AdminSeeder');

        // User
        Artisan::call('db:seed --class=UserSeeder');

        // User Email Verification
        // Artisan::call('db:seed --class=UserEmailVerificationSeeder');

        // Forget User Password Reset
        // Artisan::call('db:seed --class=ForgetUserPasswordResetSeeder');

        // Category
        Artisan::call('db:seed --class=LocationSeeder');

        // Product
        Artisan::call('db:seed --class=ProductSeeder');

        // Product variant
        Artisan::call('db:seed --class=ProductVariantSeeder');

        // Additional
        Artisan::call('db:seed --class=AdditionalSeeder');

        // Product Additional
        Artisan::call('db:seed --class=ProductAdditionalSeeder');

        // Promos
        // Artisan::call('db:seed --class=PromoSeeder');

        // Wiki
        // Artisan::call('db:seed --class=NewsletterSeeder');
    }
}
