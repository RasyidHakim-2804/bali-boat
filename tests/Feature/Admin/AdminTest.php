<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminTest extends TestCase
{
    public function test_admin_get_list_admin()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_users_id_parameter_admin_get_detail_admin()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/100000000');

        $response->assertStatus(404);
    }

    public function test_admin_get_detail_admin()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $user = Admin::where('id', '!=', 1)->first();

        $response = $this->get('/api/v1/admins/' . $user->id);

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
                'is_active',
                'deleted_at',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_users_id_parameter_admin_update_admin()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/100000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_update_admin()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/1', [
            'first_name'          => $faker->firstName(),
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600),
            'is_active'           => 1,
        ]);

        $response->assertStatus(201);
    }

    public function test_required_first_name_at_admin_update_admin()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/1', [
            'first_name'          => '',
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600),
            'is_active'           => 1,
        ]);

        $response->assertSessionHasErrors('first_name');
    }

    public function test_requires_last_name_at_admin_update_admin()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/1', [
            'last_name'           => '',
            'first_name'          => $faker->firstName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600),
            'is_active'           => 1,
        ]);

        $response->assertSessionHasErrors('last_name');
    }

    public function test_rule_in_is_active_at_admin_update_admin()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/1', [
            'first_name'          => $faker->firstName(),
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600),
            'is_active'           => 'test',
        ]);

        $response->assertSessionHasErrors('is_active');
    }

    public function test_size_profile_picture_at_admin_update_admin()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/1', [
            'first_name'          => $faker->firstName(),
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600)->size(2100),
            'is_active'           => 1,
        ]);

        $response->assertSessionHasErrors('profile_picture');
    }

    public function test_mime_profile_picture_at_admin_update_admin()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/1', [
            'first_name'          => $faker->firstName(),
            'last_name'           => $faker->lastName(),
            'profile_picture'     => UploadedFile::fake()->image('profile.png', 600, 600)->mimeType('svg'),
            'is_active'           => 1,
        ]);

        $response->assertSessionHasErrors('profile_picture');
    }

    public function test_users_id_parameter_admin_delete_admin()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/10000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_delete_admin()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $user = Admin::where('id', '!=', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/'. $user->id);

        $response->assertStatus(201);
    }
}
