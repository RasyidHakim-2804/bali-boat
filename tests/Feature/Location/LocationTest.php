<?php

namespace Tests\Feature\Location;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LocationTest extends TestCase
{
    private $url;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url = '/api/v1/locations';
    }

    public function test_get_all_location(): void
    {
        $response = $this->getJson($this->url);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data'
        ]);
    }
}
