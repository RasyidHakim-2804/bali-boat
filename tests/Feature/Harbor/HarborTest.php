<?php

namespace Tests\Feature\Harbor;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HarborTest extends TestCase
{
    private $url;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url = '/api/v1/harbors/';
    }

    public function test_get_all_harbor(): void
    {
        $response = $this->getJson($this->url);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data'
        ]);
    }
}
