<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_login_admin()
    {
        $response = $this->post('/api/v1/admins/login', [
            'email' => 'info@admin.com',
            'password' => 'admin#2022!'
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'token',
            'role'
        ]);
    }

    public function test_email_required_in_login_admin()
    {
        $response = $this->post('/api/v1/admins/login', [
            'email' => '',
            'password' => 'admin#2022!'
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_email_exists_in_login_admin()
    {
        $response = $this->post('/api/v1/admins/login', [
            'email' => 'testadmin1@admin.com',
            'password' => 'admin#2022!'
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password_required_in_login_admin()
    {
        $response = $this->post('/api/v1/admins/login', [
            'email' => 'info@admin.com',
            'password' => ''
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_exists_in_login_admin()
    {
        $response = $this->post('/api/v1/admins/login', [
            'email' => 'info@admin.com',
            'password' => 'admin#2022!1'
        ]);

        $response->assertUnprocessable('password');

        $response->assertStatus(422);
    }
}
