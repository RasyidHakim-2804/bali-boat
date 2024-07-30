<?php

namespace Tests\Feature\Newsletter;

use App\Models\Admin;
use App\Models\Newsletter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminNewsletterTest extends TestCase
{
    public function test_admin_get_list_newsletters()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/newsletters');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_users_id_parameter_admin_get_detail_newsletters()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/newsletters/100000000');

        $response->assertStatus(404);
    }

    public function test_admin_get_detail_newsletters()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $newsletter = Newsletter::first();

        $response = $this->get('/api/v1/admins/newsletters/' . $newsletter->slug);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'title',
                'message',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_users_id_parameter_admin_update_newsletters()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/newsletters/100000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_store_newsletters()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/newsletters', [
            'title'         => $faker->sentence(3),
            'message'       => $faker->sentence(),
        ]);

        $response->assertStatus(201);
    }

    public function test_can_admin_update_newsletters()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $newsletter = Newsletter::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/newsletters/' . $newsletter->slug, [
            'title'         => $faker->sentence(3),
            'message'       => $faker->sentence(),
        ]);

        $response->assertStatus(201);
    }

    public function test_title_required_at_admin_update_newsletters()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $newsletter = Newsletter::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/newsletters/' . $newsletter->slug, [
            'title'         => null,
            'message'       => $faker->sentence(),
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_title_min_char_at_admin_update_newsletters()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $newsletter = Newsletter::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/newsletters/' . $newsletter->slug, [
            'title'         => '',
            'message'       => $faker->sentence(),
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_message_required_at_admin_update_newsletters()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $newsletter = Newsletter::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/newsletters/' . $newsletter->slug, [
            'title'         => $faker->sentence(3),
            'message'       => null,
        ]);

        $response->assertSessionHasErrors('message');
    }

    public function test_message_rmin_char_at_admin_update_newsletters()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $newsletter = Newsletter::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/newsletters/' . $newsletter->slug, [
            'title'         => $faker->sentence(3),
            'message'       => '',
        ]);

        $response->assertSessionHasErrors('message');
    }

    public function test_users_id_parameter_admin_delete_newsletters()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/newsletters/10000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_delete_newsletters()
    {
        $admin      = Admin::where('id', 1)->first();
        $newsletter = Newsletter::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/newsletters/'. $newsletter->slug);

        $response->assertStatus(201);
    }

    public function test_id_parameter_admin_send_newsletters()
    {
        $admin      = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/newsletters/1000000/send');

        $response->assertStatus(201);
    }

    public function test_admin_send_newsletters()
    {
        $admin      = Admin::where('id', 1)->first();
        $newsletter = Newsletter::first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/newsletters/'. $newsletter->slug . '/send');

        $response->assertStatus(201);
    }
}
