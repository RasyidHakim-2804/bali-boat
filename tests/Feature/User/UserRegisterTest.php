<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\UserEmailVerificationToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    public function test_can_register_user()
    {
        $faker    = \Faker\Factory::create();
        $password = $faker->password(8);

        $response = $this->post('/api/v1/users/register', [
            'email'             => $faker->word() . '@gmail.com',
            'first_name'        => $faker->firstName(),
            'last_name'         => $faker->lastName(),
            'password'          => $password,
            'confirm_password'  => $password
        ]);

        $response->assertStatus(201);
    }

    public function test_email_required_in_register_user()
    {
        $faker    = \Faker\Factory::create();
        $password = $faker->password(8);

        $response = $this->post('/api/v1/users/register', [
            'email'             => '',
            'first_name'        => $faker->firstName(),
            'last_name'         => $faker->lastName(),
            'password'          => $password,
            'confirm_password'  => $password
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_email_unique_in_register_user()
    {
        $faker    = \Faker\Factory::create();
        $user   = User::first();
        $password = $faker->password(8);

        $response = $this->post('/api/v1/users/register', [
            'email'             => $user->email,
            'first_name'        => $faker->firstName(),
            'last_name'         => $faker->lastName(),
            'password'          => $password,
            'confirm_password'  => $password
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_first_name_required_in_register_user()
    {
        $faker    = \Faker\Factory::create();
        $password = $faker->password(8);

        $response = $this->post('/api/v1/users/register', [
            'email'             => $faker->email(),
            'first_name'        => '',
            'last_name'         => $faker->lastName(),
            'password'          => $password,
            'confirm_password'  => $password
        ]);

        $response->assertSessionHasErrors('first_name');
    }

    public function test_last_name_required_in_register_user()
    {
        $faker    = \Faker\Factory::create();
        $password = $faker->password(8);

        $response = $this->post('/api/v1/users/register', [
            'email'             => $faker->email(),
            'first_name'        => $faker->firstName(),
            'last_name'         => '',
            'password'          => $password,
            'confirm_password'  => $password
        ]);

        $response->assertSessionHasErrors('last_name');
    }

    public function test_password_required_in_register_user()
    {
        $faker    = \Faker\Factory::create();
        $password = $faker->password(8);

        $response = $this->post('/api/v1/users/register', [
            'email'             => $faker->email(),
            'first_name'        => $faker->firstName(),
            'last_name'         => $faker->lastName(),
            'password'          => '',
            'confirm_password'  => $password
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_min_8_in_register_user()
    {
        $faker    = \Faker\Factory::create();
        $password = $faker->password(6, 6);

        $response = $this->post('/api/v1/users/register', [
            'email'             => $faker->email(),
            'first_name'        => $faker->firstName(),
            'last_name'         => $faker->lastName(),
            'password'          => $password,
            'confirm_password'  => $password
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_same_with_confirm_password_in_register_user()
    {
        $faker    = \Faker\Factory::create();
        $password = $faker->password(8);

        $response = $this->post('/api/v1/users/register', [
            'email'             => $faker->email(),
            'first_name'        => $faker->firstName(),
            'last_name'         => $faker->lastName(),
            'password'          => $password,
            'confirm_password'  => $faker->password(8)
        ]);

        $response->assertSessionHasErrors('confirm_password');
    }

    public function test_talent_verify_registration_email_verification()
    {
        $token = UserEmailVerificationToken::first();

        $response = $this->post('/api/v1/users/email-verification/' . $token->token);

        // $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'token',
            'role'
        ]);
    }
}
