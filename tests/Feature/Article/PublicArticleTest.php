<?php

namespace Tests\Feature\Article;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PublicArticleTest extends TestCase
{
    public function test_get_list_articles()
    {
        $response = $this->get('/api/v1/articles');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_users_id_parameter_get_detail_articles()
    {
        $response = $this->get('/api/v1/articles/100000000');

        $response->assertStatus(404);
    }

    public function test_get_detail_articles()
    {
        $article = Article::first();

        $response = $this->get('/api/v1/articles/' . $article->slug);

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
