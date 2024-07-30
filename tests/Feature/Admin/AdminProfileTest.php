<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    public function test_users_get_profile()
    {
        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/profile');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'profile_picture',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_can_users_update_profile()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/profile', [
            'first_name'          => $faker->firstName(),
            'last_name'           => $faker->lastName(),
            'email'               => $faker->email(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600),
            'country'             => $faker->country(),
            'city'                => $faker->city(),
            'phone'               => $faker->phoneNumber(),
        ]);

        $response->assertStatus(201);
    }

    public function test_required_first_name_at_users_update_profile()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/profile', [
            'first_name'          => '',
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600),
            'country'             => $faker->country(),
            'city'                => $faker->city(),
            'phone'               => $faker->phoneNumber(),
        ]);

        $response->assertSessionHasErrors('first_name');
    }

    public function test_requires_last_name_at_users_update_profile()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/profile', [
            'first_name'          => $faker->firstName(),
            'last_name'           => '',
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600),
            'country'             => $faker->country(),
            'city'                => $faker->city(),
            'phone'               => $faker->phoneNumber(),
        ]);

        $response->assertSessionHasErrors('last_name');
    }

    public function test_size_profile_picture_at_users_update_profile()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/profile', [
            'first_name'          => $faker->firstName(),
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600)->size(3000),
            'country'             => $faker->country(),
            'city'                => $faker->city(),
            'phone'               => $faker->phoneNumber(),
        ]);

        $response->assertSessionHasErrors('profile_picture');
    }

    public function test_mime_profile_picture_at_users_update_profile()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/profile', [
            'first_name'          => $faker->firstName(),
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600)->mimeType('svg'),
            'country'             => $faker->country(),
            'city'                => $faker->city(),
            'phone'               => $faker->phoneNumber(),
        ]);

        $response->assertSessionHasErrors('profile_picture');
    }

    public function test_old_password_required_users_reset_password_profile()
    {
        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/reset-password', [
            'old_password'     => null,
            'password'         => 'treetal#2023!',
            'confirm_password' => 'treetal#2023!',
        ]);

        $response->assertSessionHasErrors('old_password');
    }

    public function test_old_password_exists_users_reset_password_profile()
    {
        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/reset-password', [
            'old_password'     => 'treetal#2023!',
            'password'         => 'treetal#2024!',
            'confirm_password' => 'treetal#2024!',
        ]);

        $response->assertStatus(422);
    }

    public function test_password_required_users_reset_password_profile()
    {
        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/reset-password', [
            'old_password'     => 'user#2022!',
            'password'         => null,
            'confirm_password' => null,
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_min_users_reset_password_profile()
    {
        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/reset-password', [
            'old_password'     => 'user#2022!',
            'password'         => 'treetal',
            'confirm_password' => 'treetal',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_confirm_password_required_users_reset_password_profile()
    {
        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/reset-password', [
            'old_password'     => 'user#2022!',
            'password'         => 'treetal#2023!',
            'confirm_password' => null,
        ]);

        $response->assertSessionHasErrors('confirm_password');
    }

    public function test_confirm_password_same_with_password_users_reset_password_profile()
    {
        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/reset-password', [
            'old_password'     => 'user#2022!',
            'password'         => 'treetal#2023!',
            'confirm_password' => 'treetal#2022!',
        ]);

        $response->assertSessionHasErrors('confirm_password');
    }

    public function test_users_reset_password_profile()
    {
        $admin = Admin::orderBy('id', 'DESC')->first();

        $this->be($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/reset-password', [
            'old_password'     => 'admin#2022!',
            'password'         => 'treetal#2023!',
            'confirm_password' => 'treetal#2023!',
        ]);

        $response->assertStatus(201);
    }
}
