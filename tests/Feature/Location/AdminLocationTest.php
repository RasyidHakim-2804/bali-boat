<?php

namespace Tests\Feature\Location;

use App\Models\Admin;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminLocationTest extends TestCase
{
    private $url;
    private $admin;
    private $guard;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url   = '/api/v1/admins/locations/';
        $this->admin = Admin::where('id', 1)->first();
        $this->guard = 'api_admin';
    }

    public function test_get_list_locations(): void
    {
        $response = $this->actingAs($this->admin, $this->guard)->getJson($this->url);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_get_all_locations()
    {
        $response = $this->actingAs($this->admin, $this->guard)->getJson('/api/v1/admins/locations-all/');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data'
        ]);
    }

    public function test_admin_can_store_location()
    {
        $response = $this->actingAs($this->admin, $this->guard)->postJson($this->url, [
            'name'  => 'test location'
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status'    => 'success'
        ]);
    }

    public function test_admin_can_update_location()
    {
        $location = Location::first();

        $response = $this->actingAs($this->admin, $this->guard)->putJson($this->url . $location->id, [
            'name'  => 'test location'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'    => 'success'
        ]);
    }

    public function test_admin_get_not_found_location()
    {
        $response = $this->actingAs($this->admin, $this->guard)->getJson($this->url . 'wkwkwk');

        $response->assertStatus(404);
    }

    public function test_required_name_at_admin_store_location()
    {
        $response = $this->actingAs($this->admin, $this->guard)->postJson($this->url, [
            'name'  => null
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_required_name_at_admin_update_location()
    {
        $location = Location::first();

        $response = $this->actingAs($this->admin, $this->guard)->putJson($this->url . $location->id, [
            'name'  => null
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_name_must_string_at_admin_store_location()
    {
        $response = $this->actingAs($this->admin, $this->guard)->postJson($this->url, [
            'name'  => UploadedFile::fake()->image('uploadimage', 600, 600)
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_name_must_string_at_admin_update_location()
    {
        $location = Location::first();

        $response = $this->actingAs($this->admin, $this->guard)->putJson($this->url . $location->id, [
            'name'  => UploadedFile::fake()->image('uploadimage', 600, 600)
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }
}
