<?php

namespace Tests\Feature\Ingredient;

use App\Models\Admin;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminIngredientTest extends TestCase
{
    public function test_admin_get_list_ingredients()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/ingredients');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data',
        ]);
    }

    public function test_users_id_parameter_admin_get_detail_ingredients()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/ingredients/100000000');

        $response->assertStatus(404);
    }

    public function test_admin_get_detail_ingredients()
    {
        $admin      = Admin::where('id', 1)->first();
        $ingredient = Ingredient::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/ingredients/' . $ingredient->slug);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'slug',
                'name',
                'description',
                'image',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_can_admin_store_ingredients()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/ingredients', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertStatus(201);
    }

    public function test_id_parameter_at_admin_update_ingredients()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/ingredients/100000000');

        $response->assertStatus(404);
    }

    public function test_admin_update_ingredients()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $ingredient = Ingredient::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/ingredients/' . $ingredient->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertStatus(201);
    }

    public function test_required_name_at_admin_update_ingredients()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $ingredient = Ingredient::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/ingredients/' . $ingredient->slug, [
            'name'          => null,
            'description'   => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_name_min_char_at_admin_update_ingredients()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $ingredient = Ingredient::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/ingredients/' . $ingredient->slug, [
            'name'          => '',
            'description'   => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_requires_description_at_admin_update_ingredients()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $ingredient = Ingredient::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/ingredients/' . $ingredient->slug, [
            'name'          => $faker->sentence(3),
            'description'   => null,
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_description_min_char_at_admin_update_ingredients()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $ingredient = Ingredient::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/ingredients/' . $ingredient->slug, [
            'name'          => $faker->sentence(3),
            'description'   => '',
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_rule_nullable_image_at_admin_update_ingredients()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $ingredient = Ingredient::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/ingredients/' . $ingredient->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(),
            'image'         => null,
        ]);

        $response->assertStatus(201);
    }

    // public function test_size_profile_picture_at_admin_update_ingredients()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $response = $this->put('/api/v1/admins/ingredients/' . $ingredient->slug, [
    //         'name'          => $faker->sentence(3),
    //         'description'   => $faker->sentence(),
    //         'image'         => UploadedFile::fake()->image('profile.png', 6600, 6600),
    //     ]);

    //     $response->assertSessionHasErrors('image');
    // }

    public function test_mime_profile_picture_at_admin_update_ingredients()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $ingredient = Ingredient::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/ingredients/' . $ingredient->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.pdf', 600, 600),
        ]);

        $response->assertSessionHasErrors('image');
    }

    public function test_users_id_parameter_admin_delete_ingredients()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $ingredient = Ingredient::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/ingredients/10000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_delete_ingredients()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $ingredient = Ingredient::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/ingredients/'. $ingredient->slug);

        $response->assertStatus(201);
    }
}
