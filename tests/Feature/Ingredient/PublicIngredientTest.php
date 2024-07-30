<?php

namespace Tests\Feature\Ingredient;

use App\Models\Admin;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PublicIngredientTest extends TestCase
{
    public function test_get_list_ingredients()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api');

        $response = $this->get('/api/v1/ingredients');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data',
        ]);
    }

    public function test_users_id_parameter_get_detail_ingredients()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api');

        $response = $this->get('/api/v1/ingredients/100000000');

        $response->assertStatus(404);
    }

    public function test_get_detail_ingredients()
    {
        $admin      = Admin::where('id', 1)->first();
        $ingredient = Ingredient::first();

        $this->actingAs($admin, 'api');

        $response = $this->get('/api/v1/ingredients/' . $ingredient->slug);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'slug',
                'name',
                'description',
                'image',
                'created_at',
                'updated_at'
            ]
        ]);
    }
}
