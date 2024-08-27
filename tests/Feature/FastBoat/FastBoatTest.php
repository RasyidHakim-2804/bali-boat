<?php

namespace Tests\Feature\FastBoat;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FastBoatTest extends TestCase
{   
    private $baseUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->baseUrl = '/api/v1/';
    }

    public function test_search_boats_with_morning_departure_time(): void
    {
        $response = $this->get(
            $this->baseUrl .
            'search-fast-boats?' .
            $this->generateParamFilter('departure_time', 'morning')
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'fast_boat_id',
                    'active',
                    'arrival_time',
                    'departure_time',
                    'arrival_harbor_name',
                    'departure_hartbor_name',
                    'fast_boat',
                    'variants',
                ]
            ]
        ]);
    }

    public function test_search_boats_with_exist_selected_fast_boat_id(): void
    {
        $response = $this->get(
            $this->baseUrl .
            'search-fast-boats?' .
            $this->generateParamFilter('fast_boat_id', '1')
        );
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'fast_boat_id',
                    'active',
                    'arrival_time',
                    'departure_time',
                    'arrival_harbor_name',
                    'departure_hartbor_name',
                    'fast_boat',
                    'variants',
                ]
            ]
        ]);
    }

    public function test_search_boats_with_no_exist_selected_fast_boat_id(): void
    {
        $response = $this->get(
            $this->baseUrl .
            'search-fast-boats?' .
            $this->generateParamFilter('fast_boat_id', '10000000')
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    public function test_search_boats_with_exist_selected_departure_and_arrival_harbor(): void
    {
        $response = $this->get(
            $this->baseUrl .
            'search-fast-boats?' .
            $this->generateParamFilter('from_to', '1,2')
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'fast_boat_id',
                    'active',
                    'arrival_time',
                    'departure_time',
                    'arrival_harbor_name',
                    'departure_hartbor_name',
                    'fast_boat',
                    'variants',
                ]
            ]
        ]);
    }

    public function test_search_boats_with_no_exist_selected_departure_and_arrival_harbor(): void
    {
        $response = $this->get(
            $this->baseUrl .
            'search-fast-boats?' .
            $this->generateParamFilter('from_to', '1,200000000')
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    public function test_search_boats_with_uncomplete_selected_departure_and_arrival_harbor(): void
    {
        $response = $this->get(
            $this->baseUrl .
            'search-fast-boats?' .
            $this->generateParamFilter('from_to', '1')
        );

        $response->assertStatus(500);
    }

    public function test_search_boats_with_date_yesterday(): void
    {
        $response = $this->get(
            $this->baseUrl .
            'search-fast-boats?' .
            $this->generateParamFilter('select_date', Carbon::yesterday()->format('Y-m-d'))
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    public function test_search_boats_with_date_tomorrow(): void
    {
        $response = $this->get(
            $this->baseUrl .
            'search-fast-boats?' .
            $this->generateParamFilter('select_date', Carbon::tomorrow()->format('Y-m-d'))
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'fast_boat_id',
                    'active',
                    'arrival_time',
                    'departure_time',
                    'arrival_harbor_name',
                    'departure_hartbor_name',
                    'fast_boat',
                    'variants',
                ]
            ]
        ]);
    }

    private function generateParamFilter(string $filter, string $value)
    {
        return 'filter[' . $filter .  ']=' . $value . '&';
    }

    private function generateParamSort($value)
    {
        return 'sort=' . $value . '&';
    }
}
