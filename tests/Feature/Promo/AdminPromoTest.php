<?php

namespace Tests\Feature\Ingredient;

use App\Models\Admin;
use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminPromoTest extends TestCase
{
    public function test_admin_get_list_promos()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/promos');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_users_id_parameter_admin_get_detail_promos()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/promos/100000000');

        $response->assertStatus(404);
    }

    public function test_admin_get_detail_promos()
    {
        $admin  = Admin::where('id', 1)->first();
        $promo  = Promo::where('is_active', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/promos/' . $promo->id);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'code',
                'type',
                'duration',
                'start_at',
                'expired_at',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_can_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => $faker->sentence(3),
            'type'          => 'fixed',
            'duration'      => 'repeat',
            'amount'        => 50,
            'is_active'     => 1,
            'start_at'      => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertStatus(201);
    }

    public function test_code_required_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => null,
            'description'   => $faker->sentence(3),
            'type'          => 'fixed',
            'duration'      => 'repeat',
            'is_active'     => 1,
            'start_at'     => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_code_unique_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();
        $promo = Promo::where('is_active', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $promo->code,
            'description'   => $faker->sentence(3),
            'type'          => 'fixed',
            'duration'      => 'repeat',
            'is_active'     => 1,
            'start_at'     => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_description_nullable_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'amount'        => 50,
            'type'          => 'fixed',
            'duration'      => 'repeat',
            'is_active'     => 1,
            'start_at'      => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertStatus(201);
    }

    public function test_type_required_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => null,
            'duration'      => 'repeat',
            'is_active'     => 1,
            'start_at'      => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_type_in_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'repeat',
            'is_active'     => 1,
            'start_at'     => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_duration_required_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => null,
            'is_active'     => 1,
            'start_at'      => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('duration');
    }

    public function test_duration_in_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 1,
            'start_at'     => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('duration');
    }

    public function test_is_active_required_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => null,
            'start_at'      => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('is_active');
    }

    public function test_is_active_boolean_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('is_active');
    }

    public function test_start_at_required_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => null,
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('start_at');
    }

    public function test_start_at_date_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => 'test',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('start_at');
    }

    public function test_start_at_after_or_equal_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => '2023-04-12 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('start_at');
    }

    public function test_expired_at_required_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => '2023-11-12 00:00:00',
            'expired_at'    => null,
        ]);

        $response->assertSessionHasErrors('expired_at');
    }

    public function test_expired_at_date_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => '2023-11-12 00:00:00',
            'expired_at'    => 'test',
        ]);

        $response->assertSessionHasErrors('expired_at');
    }

    public function test_expired_at_after_at_admin_store_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/promos', [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => '2023-11-12 00:00:00',
            'expired_at'    => '2023-04-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('expired_at');
    }

    public function test_id_parameter_at_admin_update_promos()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/promos/100000000');

        $response->assertStatus(404);
    }

    public function test_admin_update_promos()
    {
        $faker = \Faker\Factory::create();

        $admin  = Admin::where('id', 1)->first();
        $promo  = Promo::where('is_active', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/promos/' . $promo->id, [
            'is_active'     => 1,
            'start_at'      => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertStatus(201);
    }

    public function test_is_active_required_at_admin_update_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();
        $promo  = Promo::where('is_active', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/promos/' . $promo->id, [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => null,
            'start_at'      => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('is_active');
    }

    public function test_is_active_boolean_at_admin_update_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();
        $promo  = Promo::where('is_active', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/promos/' . $promo->id, [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => '2023-11-11 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('is_active');
    }

    public function test_start_at_required_at_admin_update_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();
        $promo  = Promo::where('is_active', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/promos/' . $promo->id, [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => null,
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('start_at');
    }

    public function test_start_at_date_at_admin_update_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();
        $promo  = Promo::where('is_active', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/promos/' . $promo->id, [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => 'test',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('start_at');
    }

    public function test_start_at_after_or_equal_at_admin_update_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();
        $promo  = Promo::where('is_active', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/promos/' . $promo->id, [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => '2023-04-12 00:00:00',
            'expired_at'    => '2023-11-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('start_at');
    }

    public function test_expired_at_required_at_admin_update_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();
        $promo  = Promo::where('is_active', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/promos/' . $promo->id, [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => '2023-11-12 00:00:00',
            'expired_at'    => null,
        ]);

        $response->assertSessionHasErrors('expired_at');
    }

    public function test_expired_at_date_at_admin_update_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();
        $promo  = Promo::where('is_active', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/promos/' . $promo->id, [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => '2023-11-12 00:00:00',
            'expired_at'    => 'test',
        ]);

        $response->assertSessionHasErrors('expired_at');
    }

    public function test_expired_at_after_at_admin_update_promos()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();
        $promo  = Promo::where('is_active', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/promos/' . $promo->id, [
            'code'          => $faker->sentence(1),
            'description'   => null,
            'type'          => 'first',
            'duration'      => 'forever',
            'is_active'     => 'yes',
            'start_at'      => '2023-11-12 00:00:00',
            'expired_at'    => '2023-04-12 00:00:00',
        ]);

        $response->assertSessionHasErrors('expired_at');
    }

    public function test_users_id_parameter_admin_delete_promos()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/promos/10000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_delete_promos()
    {
        $faker = \Faker\Factory::create();

        $admin      = Admin::where('id', 1)->first();
        $promo  = Promo::where('is_active', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/promos/'. $promo->id);

        $response->assertStatus(201);
    }
}
