<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    public function test_users_get_profile()
    {
        $user = User::first();

        $this->actingAs($user, 'api_user');

        $response = $this->get('/api/v1/users/profile');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'profile_picture',
                'country',
                'city',
                'phone',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_can_users_update_profile()
    {
        $faker = \Faker\Factory::create();

        $user = User::first();

        $this->actingAs($user, 'api_user');

        $response = $this->put('/api/v1/users/profile', [
            'first_name'          => $faker->firstName(),
            'last_name'           => $faker->lastName(),
            'email'               => $faker->email(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600),
            'country'             => $faker->country(),
            'city'                => $faker->city(),
            'phone'               => $faker->phoneNumber(),
            'is_profile_picture_deleted' => 0,
        ]);

        $response->assertStatus(201);
    }

    public function test_required_first_name_at_users_update_profile()
    {
        $faker = \Faker\Factory::create();

        $user = User::first();

        $this->actingAs($user, 'api_user');

        $response = $this->put('/api/v1/users/profile', [
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

        $user = User::first();

        $this->actingAs($user, 'api_user');

        $response = $this->put('/api/v1/users/profile', [
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

        $user = User::first();

        $this->actingAs($user, 'api_user');

        $response = $this->put('/api/v1/users/profile', [
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

        $user = User::first();

        $this->actingAs($user, 'api_user');

        $response = $this->put('/api/v1/users/profile', [
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
        $user = User::first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/reset-password', [
            'old_password'     => null,
            'password'         => 'treetal#2023!',
            'confirm_password' => 'treetal#2023!',
        ]);

        $response->assertStatus(422);
    }

    public function test_old_password_exists_users_reset_password_profile()
    {
        $user = User::first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/reset-password', [
            'old_password'     => 'treetal#2023!',
            'password'         => 'treetal#2024!',
            'confirm_password' => 'treetal#2024!',
        ]);

        $response->assertStatus(422);
    }

    public function test_password_required_users_reset_password_profile()
    {
        $user = User::first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/reset-password', [
            'old_password'     => 'user#2022!',
            'password'         => null,
            'confirm_password' => null,
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_min_users_reset_password_profile()
    {
        $user = User::first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/reset-password', [
            'old_password'     => 'user#2022!',
            'password'         => 'treetal',
            'confirm_password' => 'treetal',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_confirm_password_required_users_reset_password_profile()
    {
        $user = User::first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/reset-password', [
            'old_password'     => 'user#2022!',
            'password'         => 'treetal#2023!',
            'confirm_password' => null,
        ]);

        $response->assertSessionHasErrors('confirm_password');
    }

    public function test_confirm_password_same_with_password_users_reset_password_profile()
    {
        $user = User::first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/reset-password', [
            'old_password'     => 'user#2022!',
            'password'         => 'treetal#2023!',
            'confirm_password' => 'treetal#2022!',
        ]);

        $response->assertSessionHasErrors('confirm_password');
    }

    public function test_users_reset_password_profile()
    {
        $user = User::where('id', '<', 14)->first();

        $this->be($user, 'api_user');

        $response = $this->post('/api/v1/users/reset-password', [
            'old_password'     => 'user#2022!',
            'password'         => 'treetal#2023!',
            'confirm_password' => 'treetal#2023!',
        ]);

        $response->assertStatus(201);
    }

    public function test_google_auth_users_update_profile()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('password', null)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/reset-password', [
            'old_password'     => null,
            'password'         => 'treetal#2023!',
            'confirm_password' => 'treetal#2023!',
        ]);

        $response->assertStatus(201);
    }
}
