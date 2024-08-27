<?php

namespace Tests\Feature\FastBoat;

use App\Models\Admin;
use App\Models\FastBoat;
use App\Models\FastBoatScheduleVariant;
use App\Models\Harbor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AdminScheduleTest extends TestCase
{
    private string $url;
    private string $guard;
    private Admin $admin;
    private FastBoat $boat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->boat          = FastBoat::first();
        $this->url           = '/api/v1/admins/fast-boats/' . $this->boat->slug . '/schedules/';
        $this->admin         = Admin::where('id', 1)->first();
        $this->guard         = 'api_admin';
    }

    private function getValidRequest()
    {
        return [
            'departure_harbor_id'   => 1,
            'arrival_harbor_id'     => 2,
            'departure_time'        => '07:00',
            'arrival_time'          => '08:00',
            'active'                => 1,
            'variants'              => [
                'local'     => [
                    'adult'     => 50000,
                    'child'     => 45000,
                ],
                'foreign'   => [
                    'adult'     => 60000,
                    'child'     => 55000,
                ],
            ],
        ];
    }

    public function test_admin_get_list_schedules_with_pagination()
    {
        $response = $this->actingAs($this->admin, $this->guard)->getJson($this->url);

        $response->assertStatus(200)->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_admin_get_schedule_detail()
    {
        $schedule = $this->boat->schedules()->first();
        $response = $this->actingAs($this->admin, $this->guard)->getJson($this->url . $schedule->id);

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                'id',
                'fast_boat_id',
                'active',
                'arrival_time',
                'departure_time',
                'arrival_harbor_name',
                'departure_hartbor_name',
                'variants',
            ],
        ]);
    }

    public function test_admin_get_not_found_schedule_detail()
    {
        $response = $this->actingAs($this->admin, $this->guard)->getJson($this->url . 'ok');

        $response->assertStatus(404);
    }

    public function test_admin_can_store_schedule()
    {
        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url, 
            $this->getValidRequest()
        );

        $response->assertStatus(201)->assertJson([
            'status' => 'success'
        ]);
    }

    public function test_admin_can_update_schedule()
    {
        $schedule = $this->boat->schedules()->first();

        $response = $this->actingAs($this->admin, $this->guard)->putJson(
            $this->url . $schedule->id, 
            $this->getValidRequest()
        );

        $response->assertStatus(200)->assertJson([
            'status' => 'success'
        ]);
    }

    // Test request rule

    public function test_all_field_is_required()
    {
        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url, 
            []
        );

        $response->assertStatus(422)->assertJsonValidationErrors([
            'departure_harbor_id',
            'arrival_harbor_id',
            'departure_time',
            'arrival_time',
            'active',
            'variants.local.adult',
            'variants.local.child',
            'variants.foreign.adult',
            'variants.foreign.child'
        ]);
    }

    public function test_arrival_harbor_id_must_exist_at_admin_store_or_update_schedule()
    {
        $request  = $this->getValidRequest();
        $request['arrival_harbor_id'] = 0;

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('arrival_harbor_id');
    }

    public function test_departure_harbor_id_must_exist_at_admin_store_or_update_schedule()
    {
        $request  = $this->getValidRequest();
        $request['departure_harbor_id'] = 0;

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('departure_harbor_id');
    }

    public function test_arrival_harbor_id_must_different_from_departure_harbor_id_at_admin_store_or_update_schedule()
    {
        $request  = $this->getValidRequest();
        $request['arrival_harbor_id'] = $request['departure_harbor_id'];

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('arrival_harbor_id');
    }

    public function test_departure_time_must_date_with_format_H_i_at_admin_store_or_update_schedule()
    {
        $request = $this->getValidRequest();
        $request['departure_time'] = 'ok gan';

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url
        );

        $response->assertStatus(422)->assertJsonValidationErrors('departure_time');
    }

    public function test_arrival_time_must_date_with_format_H_i_at_admin_store_or_update_schedule()
    {
        $request = $this->getValidRequest();
        $request['arrival_time'] = 'ok gan';

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url
        );

        $response->assertStatus(422)->assertJsonValidationErrors('arrival_time');
    }

    public function test_arrival_time_must_different_from_departure_time_at_admin_store_or_update_schedule()
    {
        $request  = $this->getValidRequest();
        $request['arrival_time'] = $request['departure_time'];

        $response = $this->actingAs($this->admin, $this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('arrival_time');
    }

    public function test_active_must_bool_at_admin_store_or_update_schedule()
    {
        $request = $this->getValidRequest();
        $request['active']  = 'ok';

        $response = $this->actingAs($this->admin,$this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('active');
    }

    public function test_variants_must_numeric_at_admin_store_or_update_schedule()
    {
        $request = $this->getValidRequest();
        $request['variants']['local']['adult']  = 'ok';

        $response = $this->actingAs($this->admin,$this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('variants.local.adult');
    }

    public function test_variants_must_min_0_at_admin_store_or_update_schedule()
    {
        $request = $this->getValidRequest();
        $request['variants']['local']['adult']  = -9;

        $response = $this->actingAs($this->admin,$this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('variants.local.adult');
    }

    public function test_variants_must_max_1000000000_at_admin_store_or_update_schedule()
    {
        $request = $this->getValidRequest();
        $request['variants']['local']['adult']  = '1000000001';

        $response = $this->actingAs($this->admin,$this->guard)->postJson(
            $this->url,
            $request
        );

        $response->assertStatus(422)->assertJsonValidationErrors('variants.local.adult');
    }
}
