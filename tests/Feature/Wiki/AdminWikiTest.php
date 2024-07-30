<?php

namespace Tests\Feature\Wiki;

use App\Models\Admin;
use App\Models\Wiki;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminWikiTest extends TestCase
{
    public function test_admin_get_list_wikis()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/wikis');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_can_admin_store_wikis()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/wikis', [
            'title'         => $faker->sentence(3),
            'description'   => $faker->sentence(),
            'category'      => $faker->sentence(1)
        ]);

        $response->assertStatus(201);
    }

    public function test_users_id_parameter_admin_get_detail_wikis()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/wikis/100000000');

        $response->assertStatus(404);
    }

    public function test_admin_get_detail_wikis()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Wiki::first();

        $response = $this->get('/api/v1/admins/wikis/' . $article->slug);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'title',
                'description',
                'category',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_can_admin_update_wikis()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Wiki::first();

        $response = $this->put('/api/v1/admins/wikis/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'description'   => $faker->sentence(),
            'category'      => $faker->sentence(1),
        ]);

        $response->assertStatus(201);
    }

    public function test_title_required_at_admin_update_wikis()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Wiki::first();

        $response = $this->put('/api/v1/admins/wikis/' . $article->slug, [
            'title'         => null,
            'description'   => $faker->sentence(),
            'category'      => $faker->sentence(1),
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_title_min_char_at_admin_update_wikis()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Wiki::first();

        $response = $this->put('/api/v1/admins/wikis/' . $article->slug, [
            'title'         => '',
            'description'   => $faker->sentence(),
            'category'      => $faker->sentence(1),
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_description_required_at_admin_update_wikis()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Wiki::first();

        $response = $this->put('/api/v1/admins/wikis/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'description'   => null,
            'category'      => $faker->sentence(1),
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_description_min_char_at_admin_update_wikis()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Wiki::first();

        $response = $this->put('/api/v1/admins/wikis/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'description'   => '',
            'category'      => $faker->sentence(1),
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_category_required_at_admin_update_wikis()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Wiki::first();

        $response = $this->put('/api/v1/admins/wikis/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'description'   => $faker->sentence(),
            'category'      => null,
        ]);

        $response->assertSessionHasErrors('category');
    }

    public function test_category_min_char_at_admin_update_wikis()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Wiki::first();

        $response = $this->put('/api/v1/admins/wikis/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'description'   => $faker->sentence(),
            'category'      => '',
        ]);

        $response->assertSessionHasErrors('category');
    }

    public function test_users_id_parameter_admin_delete_wikis()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/wikis/10000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_delete_wikis()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $article = Wiki::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/wikis/'. $article->slug);

        $response->assertStatus(201);
    }
}
