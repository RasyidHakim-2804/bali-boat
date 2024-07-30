<?php

namespace Tests\Feature\Article;

use App\Models\Admin;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminArticleTest extends TestCase
{
    public function test_admin_get_list_articles()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/articles');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_can_admin_store_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/articles', [
            'title'         => $faker->sentence(3),
            'body'          => $faker->sentence(),
            'category'      => $faker->sentence(1),
            'meta'          => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertStatus(201);
    }

    public function test_users_id_parameter_admin_get_detail_articles()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/articles/100000000');

        $response->assertStatus(404);
    }

    public function test_admin_get_detail_articles()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Article::first();

        $response = $this->get('/api/v1/admins/articles/' . $article->slug);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'title',
                'body',
                'category',
                'meta',
                'image',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_can_admin_update_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Article::first();

        $response = $this->put('/api/v1/admins/articles/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'body'          => $faker->sentence(),
            'category'      => $faker->sentence(1),
            'meta'          => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertStatus(201);
    }

    public function test_title_required_at_admin_update_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Article::first();

        $response = $this->put('/api/v1/admins/articles/' . $article->slug, [
            'title'         => null,
            'body'          => $faker->sentence(),
            'category'      => $faker->sentence(1),
            'meta'          => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_title_min_char_at_admin_update_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Article::first();

        $response = $this->put('/api/v1/admins/articles/' . $article->slug, [
            'title'         => '',
            'body'          => $faker->sentence(),
            'category'      => $faker->sentence(1),
            'meta'          => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_body_required_at_admin_update_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Article::first();

        $response = $this->put('/api/v1/admins/articles/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'body'          => null,
            'category'      => $faker->sentence(1),
            'meta'          => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertSessionHasErrors('body');
    }

    public function test_body_min_char_at_admin_update_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Article::first();

        $response = $this->put('/api/v1/admins/articles/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'body'          => '',
            'category'      => $faker->sentence(1),
            'meta'          => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertSessionHasErrors('body');
    }

    public function test_category_required_at_admin_update_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Article::first();

        $response = $this->put('/api/v1/admins/articles/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'body'          => $faker->sentence(),
            'category'      => null,
            'meta'          => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertSessionHasErrors('category');
    }

    public function test_category_min_char_at_admin_update_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Article::first();

        $response = $this->put('/api/v1/admins/articles/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'body'          => $faker->sentence(),
            'category'      => '',
            'meta'          => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertSessionHasErrors('category');
    }

    public function test_meta_required_at_admin_update_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Article::first();

        $response = $this->put('/api/v1/admins/articles/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'body'          => $faker->sentence(),
            'category'      => $faker->sentence(1),
            'meta'          => null,
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertSessionHasErrors('meta');
    }

    public function test_meta_min_char_at_admin_update_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Article::first();

        $response = $this->put('/api/v1/admins/articles/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'body'          => $faker->sentence(),
            'category'      => $faker->sentence(1),
            'meta'          => '',
            'image'         => UploadedFile::fake()->image('profile.png', 600, 600),
        ]);

        $response->assertSessionHasErrors('meta');
    }

    public function test_rule_image_required_at_admin_update_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Article::first();

        $response = $this->put('/api/v1/admins/articles/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'body'          => $faker->sentence(),
            'category'      => $faker->sentence(1),
            'meta'          => $faker->sentence(),
            'image'         => null,
        ]);

        $response->assertSessionHasErrors('image');
    }

    // public function test_size_profile_picture_at_admin_update_articles()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $response = $this->put('/api/v1/admins/articles/' . $article->slug, [
    //         'name'          => $faker->sentence(3),
    //         'description'   => $faker->sentence(),
    //         'image'         => UploadedFile::fake()->image('profile.png', 6600, 6600),
    //     ]);

    //     $response->assertSessionHasErrors('image');
    // }

    public function test_mime_profile_picture_at_admin_update_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $article = Article::first();

        $response = $this->put('/api/v1/admins/articles/' . $article->slug, [
            'title'         => $faker->sentence(3),
            'body'          => $faker->sentence(),
            'category'      => $faker->sentence(1),
            'meta'          => $faker->sentence(),
            'image'         => UploadedFile::fake()->image('profile.pdf', 600, 600),
        ]);

        $response->assertSessionHasErrors('image');
    }

    public function test_users_id_parameter_admin_delete_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/articles/10000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_delete_articles()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $article = Article::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/articles/'. $article->slug);

        $response->assertStatus(201);
    }
}
