<?php

namespace Tests\Feature\FastBoat;

use App\Models\Admin;
use App\Models\FastBoat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminFastBoatTest extends TestCase
{
    private $url;
    private $admin;
    private $guard;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url          = '/api/v1/admins/fast-boats/';
        $this->admin        = Admin::where('id', 1)->first();
        $this->guard        = 'api_admin';
    }

    private function getValidRequest()
    {
        return [
            'name'      => 'test fast boat',
            'active'    => 1,
            'rating'    => 5,
            'image'     => UploadedFile::fake()->image('image.png', 600, 600),
        ];
    }

    public function test_admin_get_list_boats(): void
    {
        $response = $this->actingAs($this->admin, $this->guard)->getJson($this->url);

        $response->assertStatus(200)->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_admin_get_detail_boat()
    {
        $boat     = FastBoat::first();

        $response = $this->actingAs($this->admin, $this->guard)->getJson($this->url . $boat->slug);

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'image',
                'active',
                'rating',
                'created_at',
                'updated_at',
                'schedules_count',
            ]
        ]);
    }

    public function test_admin_get_not_found_boat()
    {
        $response = $this->actingAs($this->admin, $this->guard)->getJson($this->url . 'wkwkwk');

        $response->assertStatus(404);
    }

    public function test_admin_can_store_boat()
    {
        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url,
            $this->getValidRequest()
        );

        $response->assertStatus(201)->assertJson([
            'status' => 'success'
        ]);
    }

    public function test_admin_can_update_boat()
    {
        $boat = FastBoat::first();

        $response = $this->actingAs($this->admin, $this->guard)->putJson(
            $this->url . $boat->slug,
            $this->getValidRequest()
        );

        $response->assertStatus(200)->assertJson([
            'status' => 'success'
        ]);
    }

    public function test_admin_required_name_at_boat()
    {
        $request         = $this->getValidRequest();
        $request['name'] = null;

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('name');
    }

    public function test_admin_name_must_string_at_boat()
    {
        $request         = $this->getValidRequest();
        $request['name'] = UploadedFile::fake()->image('profile.png', 600,600);

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('name');
    }

    public function test_admin_active_must_bool_at_boat()
    {
        $request           = $this->getValidRequest();
        $request['active'] = 3;

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('active');
    }

    public function test_admin_active_is_required_at_boat()
    {
        $request           = $this->getValidRequest();
        $request['active'] = null;

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('active');
    }

    public function test_admin_image_is_required_at_boat()
    {
        $request          = $this->getValidRequest();
        $request['image'] = null;

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('image');
    }

    public function test_admin_image_must_image_at_boat()
    {
        $request          = $this->getValidRequest();
        $request['image'] = 'null';

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('image');
    }

    public function test_admin_image_mime_at_boat()
    {
        $request          = $this->getValidRequest();
        $request['image'] = UploadedFile::fake()->image('image.pdf', 600, 600);

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('image');
    }

    public function test_image_can_nullable_at_update()
    {
        $boat             = FastBoat::first();
        $request          = $this->getValidRequest();
        $request['image'] = null;

        $response = $this->actingAs($this->admin, $this->guard)->putJson(
            $this->url . $boat->slug,
            $request
        );

        $response->assertStatus(200);
    }

    public function test_required_rating_at_admin_store_or_update_fast_boat()
    {
        $boat             = FastBoat::first();
        $request          = $this->getValidRequest();
        $request['rating'] = null;

        $response = $this->actingAs($this->admin, $this->guard)->putJson(
            $this->url . $boat->slug,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('rating');
    }

    public function test_rating_must_numeric_at_admin_store_or_update_fast_boat()
    {
        $boat             = FastBoat::first();
        $request          = $this->getValidRequest();
        $request['rating'] = 'null';

        $response = $this->actingAs($this->admin, $this->guard)->putJson(
            $this->url . $boat->slug,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('rating');
    }

    public function test_rating_must_min_0_at_admin_store_or_update_fast_boat()
    {
        $boat             = FastBoat::first();
        $request          = $this->getValidRequest();
        $request['rating'] = -9;

        $response = $this->actingAs($this->admin, $this->guard)->putJson(
            $this->url . $boat->slug,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('rating');
    }

    public function test_rating_must_max_5_at_admin_store_or_update_fast_boat()
    {
        $boat             = FastBoat::first();
        $request          = $this->getValidRequest();
        $request['rating'] = 9;

        $response = $this->actingAs($this->admin, $this->guard)->putJson(
            $this->url . $boat->slug,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('rating');
    }
}
