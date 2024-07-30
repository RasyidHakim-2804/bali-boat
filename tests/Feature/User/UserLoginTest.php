<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_login_user()
    {
        $user = User::first();
        
        $response = $this->post('/api/v1/users/login', [
            'email'    => $user->email,
            'password' => 'user#2022!'
        ]);

        $response->assertStatus(200);
    }

    public function test_email_required_in_login_user()
    {
        $response = $this->post('/api/v1/users/login', [
            'email'    => '',
            'password' => 'user#2022!'
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_email_exists_in_login_user()
    {
        $response = $this->post('/api/v1/users/login', [
            'email'    => 'testuser1@user.com',
            'password' => 'user#2022!'
        ]);

        $response->assertStatus(422);
    }

    public function test_password_required_in_login_user()
    {
        $response = $this->post('/api/v1/users/login', [
            'email'    => 'testuser1@user.com',
            'password' => ''
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_password_exists_in_login_user()
    {
        $response = $this->post('/api/v1/users/login', [
            'email'    => 'testuser@user.com',
            'password' => 'user#2022!1'
        ]);

        $response->assertUnprocessable();

        $response->assertStatus(422);
    }
}
