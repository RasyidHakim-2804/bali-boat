<?php

namespace Tests\Feature\Wiki;

use App\Models\Wiki;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PublicWikiTest extends TestCase
{
    public function test_admin_get_list_articles()
    {
        $response = $this->get('/api/v1/articles');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_article_id_parameter_at_get_detail_articles()
    {
        $response = $this->get('/api/v1/wiki/100000000');

        $response->assertStatus(404);
    }

    public function test_get_detail_articles()
    {
        $article = Wiki::first();

        $response = $this->get('/api/v1/wiki/' . $article->slug);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'title',
                'body',
                'category',
                'meta',
                'image',
                'created_at',
                'updated_at'
            ]
        ]);
    }
}
