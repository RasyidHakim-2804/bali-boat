<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    public function test_admin_get_list_user()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/users');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_users_id_parameter_admin_get_detail_user()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/users/100000000');

        $response->assertStatus(404);
    }

    public function test_admin_get_detail_user()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $user = User::first();

        $response = $this->get('/api/v1/admins/users/' . $user->uuid);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'uuid',
                'profile_picture',
                'country',
                'city',
                'phone',
                'is_active',
                'deleted_at',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_users_id_parameter_admin_update_user()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/users/100000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_update_user()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $user = User::where('id', 1)->first();

        $response = $this->put('/api/v1/admins/users/' . $user->uuid, [
            'first_name'          => $faker->firstName(),
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600),
            'country'             => $faker->country(),
            'city'                => $faker->city(),
            'phone'               => $faker->phoneNumber(),
            'is_active'           => 1,
        ]);

        $response->assertStatus(201);
    }

    public function test_required_first_name_at_admin_update_user()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $user = User::where('id', 1)->first();

        $response = $this->put('/api/v1/admins/users/' . $user->uuid, [
            'first_name'          => '',
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600),
            'country'             => $faker->country(),
            'city'                => $faker->city(),
            'phone'               => $faker->phoneNumber(),
            'is_active'           => 1,
        ]);

        $response->assertSessionHasErrors('first_name');
    }

    public function test_requires_last_name_at_admin_update_user()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $user = User::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/users/' . $user->uuid, [
            'last_name'           => '',
            'first_name'          => $faker->firstName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600),
            'country'             => $faker->country(),
            'city'                => $faker->city(),
            'phone'               => $faker->phoneNumber(),
            'is_active'           => 1,
        ]);

        $response->assertSessionHasErrors('last_name');
    }

    public function test_rule_in_is_active_at_admin_update_user()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $user = User::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/users/' . $user->uuid, [
            'first_name'          => $faker->firstName(),
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600),
            'country'             => $faker->country(),
            'city'                => $faker->city(),
            'phone'               => $faker->phoneNumber(),
            'is_active'           => 'test',
        ]);

        $response->assertSessionHasErrors('is_active');
    }

    public function test_size_profile_picture_at_admin_update_user()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $user = User::where('id', 1)->first();

        $response = $this->put('/api/v1/admins/users/' . $user->uuid, [
            'first_name'          => $faker->firstName(),
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600)->size(2100),
            'country'             => $faker->country(),
            'city'                => $faker->city(),
            'phone'               => $faker->phoneNumber(),
            'is_active'           => 1,
        ]);

        $response->assertSessionHasErrors('profile_picture');
    }

    public function test_mime_profile_picture_at_admin_update_user()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $user = User::where('id', 1)->first();

        $response = $this->put('/api/v1/admins/users/' . $user->uuid, [
            'first_name'          => $faker->firstName(),
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600)->mimeType('svg'),
            'country'             => $faker->country(),
            'city'                => $faker->city(),
            'phone'               => $faker->phoneNumber(),
            'is_active'           => 1,
        ]);

        $response->assertSessionHasErrors('profile_picture');
    }

    public function test_users_id_parameter_admin_delete_user()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/users/10000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_delete_user()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $user = User::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/users/'. $user->uuid);

        $response->assertStatus(201);
    }
}
