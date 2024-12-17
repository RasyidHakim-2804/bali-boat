<?php

namespace Tests\Feature\Harbor;

use App\Models\Admin;
use App\Models\Harbor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminHarborTest extends TestCase
{
    private $url;
    private $admin;
    private $guard;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url   = '/api/v1/admins/harbors/';
        $this->admin = Admin::where('id', 1)->first();
        $this->guard = 'api_admin';
    }

    public function test_get_list_harbors(): void
    {
        $response = $this->actingAs($this->admin, $this->guard)->getJson($this->url);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_get_all_harbors()
    {
        $response = $this->actingAs($this->admin, $this->guard)->getJson('/api/v1/admins/harbors-all/');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data'
        ]);
    }

    public function test_admin_can_store_harbor()
    {
        $response = $this->actingAs($this->admin, $this->guard)->postJson($this->url, [
            'name'  => 'test harbor'
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status'    => 'success'
        ]);
    }

    public function test_admin_can_update_harbor()
    {
        $harbor = Harbor::first();

        $response = $this->actingAs($this->admin, $this->guard)->putJson($this->url . $harbor->id, [
            'name'  => 'test harbor'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'    => 'success'
        ]);
    }

    public function test_admin_get_not_found_harbor()
    {
        $response = $this->actingAs($this->admin, $this->guard)->getJson($this->url . 'wkwkwk');

        $response->assertStatus(404);
    }

    public function test_required_name_at_admin_store_harbor()
    {
        $response = $this->actingAs($this->admin, $this->guard)->postJson($this->url, [
            'name'  => null
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_required_name_at_admin_update_harbor()
    {
        $harbor = Harbor::first();

        $response = $this->actingAs($this->admin, $this->guard)->putJson($this->url . $harbor->id, [
            'name'  => null
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_name_must_string_at_admin_store_harbor()
    {
        $response = $this->actingAs($this->admin, $this->guard)->postJson($this->url, [
            'name'  => UploadedFile::fake()->image('uploadimage', 600, 600)
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_name_must_string_at_admin_update_harbor()
    {
        $harbor = Harbor::first();

        $response = $this->actingAs($this->admin, $this->guard)->putJson($this->url . $harbor->id, [
            'name'  => UploadedFile::fake()->image('uploadimage', 600, 600)
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }
}
