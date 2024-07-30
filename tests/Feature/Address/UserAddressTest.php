<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UserAddressTest extends TestCase
{
    public function test_can_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => '417 Montgomery Street',
            'street2'           => '417 Montgomery Street',
            'city'              => 'San Francisco',
            'state'             => 'CA',
            'zip'               => '94104',
            'country'           => 'United States',
            'email'             => $faker->word() . '@gmail.com',
        ]);

        $response->assertStatus(201);
    }

    public function test_can_user_store_international_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => '417 Montgomery Street',
            'street2'           => '417 Montgomery Street',
            'city'              => 'Banjar',
            'state'             => 'BL',
            'zip'               => '12345',
            'country'           => 'Indonesia',
            'email'             => $faker->word() . '@gmail.com',
        ]);

        $response->assertStatus(201);
    }

    public function test_user_get_detail_address()
    {
        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->get('/api/v1/users/' . $user->uuid . '/addresses/');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'recipient_name',
                'phone',
                'street1',
                'street2',
                'city',
                'state',
                'country',
                'zip',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_required_phone_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => null,
            'street1'           => '417 Montgomery Street',
            'street2'           => '417 Montgomery Street',
            'city'              => 'San Francisco',
            'state'             => 'CA',
            'zip'               => '94104',
            'country'           => 'United States',
            'email'             => $faker->word() . '@gmail.com',
        ]);

        $response->assertSessionHasErrors('phone');
    }

    public function test_required_street1_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => null,
            'street2'           => '417 Montgomery Street',
            'city'              => 'San Francisco',
            'state'             => 'CA',
            'zip'               => '94104',
            'country'           => 'United States',
            'email'             => $faker->word() . '@gmail.com',
        ]);

        $response->assertSessionHasErrors('street1');
    }

    public function test_invalid_street1_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => 'Test',
            'street2'           => '417 Montgomery Street',
            'city'              => 'San Francisco',
            'state'             => 'CA',
            'zip'               => '94104',
            'country'           => 'United States',
            'email'             => $faker->word() . '@gmail.com',
        ]);

        $response->assertStatus(201);
    }

    public function test_required_city_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => '417 Montgomery Street',
            'street2'           => '417 Montgomery Street',
            'city'              => null,
            'state'             => 'CA',
            'zip'               => '94104',
            'country'           => 'United States',
            'email'             => $faker->word() . '@gmail.com',
        ]);

        $response->assertSessionHasErrors('city');
    }

    public function test_required_state_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => '417 Montgomery Street',
            'street2'           => '417 Montgomery Street',
            'city'              => 'San Francisco',
            'state'             => null,
            'zip'               => '94104',
            'country'           => 'United States',
            'email'             => $faker->word() . '@gmail.com',
        ]);

        $response->assertSessionHasErrors('state');
    }

    public function test_min_state_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => '417 Montgomery Street',
            'street2'           => '417 Montgomery Street',
            'city'              => 'San Francisco',
            'state'             => 'W',
            'zip'               => '94104',
            'country'           => 'United States',
            'email'             => $faker->word() . '@gmail.com',
        ]);

        $response->assertSessionHasErrors('state');
    }

    public function test_max_state_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => '417 Montgomery Street',
            'street2'           => '417 Montgomery Street',
            'city'              => 'San Francisco',
            'state'             => 'CAC',
            'zip'               => '94104',
            'country'           => 'United States',
            'email'             => $faker->word() . '@gmail.com',
        ]);

        $response->assertSessionHasErrors('state');
    }

    public function test_required_country_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => '417 Montgomery Street',
            'street2'           => '417 Montgomery Street',
            'city'              => 'San Francisco',
            'state'             => 'CAC',
            'zip'               => '94104',
            'country'           => null,
            'email'             => $faker->word() . '@gmail.com',
        ]);

        $response->assertSessionHasErrors('country');
    }

    public function test_required_email_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => '417 Montgomery Street',
            'street2'           => '417 Montgomery Street',
            'city'              => 'San Francisco',
            'state'             => 'CAC',
            'zip'               => '94104',
            'country'           => 'United States',
            'email'             => null,
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_validate_email_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => '417 Montgomery Street',
            'street2'           => '417 Montgomery Street',
            'city'              => 'San Francisco',
            'state'             => 'CAC',
            'zip'               => '94104',
            'country'           => 'United States',
            'email'             => 'test',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_invalid_state_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => 'qwqdqdqdq',
            'city'              => 'San Francisco',
            'state'             => 'CA',
            'zip'               => '12345',
            'country'           => 'United States',
            'email'             => 'test@gmail.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_invalid_city_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => 'qwqdqdqdq',
            'city'              => 'San Francisco',
            'state'             => 'NY',
            'zip'               => '12345',
            'country'           => 'United States',
            'email'             => 'test@gmail.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_invalid_zip_at_user_store_address()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('id', 3)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->post('/api/v1/users/' . $user->uuid . '/addresses', [
            'recipient_name'    => $faker->sentence(3),
            'phone'             => '4151234567',
            'street1'           => 'qwqdqdqdq',
            'city'              => 'San Francisco',
            'state'             => 'CA',
            'zip'               => '77777',
            'country'           => 'United States',
            'email'             => 'test@gmail.com',
        ]);

        $response->assertStatus(422);
    }

    // public function test_users_id_parameter_user_delete_address()
    // {
    //     $faker = \Faker\Factory::create();

    //     $user = User::where('id', 3)->first();

    //     $this->actingAs($user, 'api_user');

    //     $response = $this->delete('/api/v1/users/' . $user->uuid . '/addresses0000000');

    //     $response->assertStatus(404);
    // }

    // public function test_can_user_delete_address()
    // {
    //     $faker = \Faker\Factory::create();

    //     $user = User::where('id', 3)->first();

    //     $address = Address::orderBy('id', 'DESC')->where('user_id', $user->id)->first();

    //     $this->actingAs($user, 'api_user');

    //     $response = $this->delete('/api/v1/users/' . $user->uuid . '/addresses/'. $address->id);

    //     $response->assertStatus(201);
    // }
}
