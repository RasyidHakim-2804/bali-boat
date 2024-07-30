<?php

namespace Tests\Feature\Ingredient;

use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ApplyPromoTest extends TestCase
{
    public function test_code_required_at_user_apply_promos()
    {
        $promo = Promo::where('is_active', 1)->first();

        $response = $this->post('/api/v1/apply-promo', [
            'code'          => null,
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_code_exists_at_user_apply_promos()
    {
        $response = $this->post('/api/v1/apply-promo', [
            'code'          => 'SGAHD667',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_user_apply_promos()
    {
        $promo = Promo::where('is_active', 1)->first();

        $response = $this->post('/api/v1/apply-promo', [
            'code'          => $promo->code,
        ]);

        $response->assertStatus(200);
    }

    public function test_is_active_true_at_user_apply_promos()
    {
        $promo = Promo::where('is_active', 0)->first();

        $response = $this->post('/api/v1/apply-promo', [
            'code'          => $promo->code,
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_start_at_at_at_user_apply_promos()
    {
        $promo = Promo::where('start_at', '>', now())->first();

        $response = $this->post('/api/v1/apply-promo', [
            'code'          => $promo->code,
        ]);

        $response->assertStatus(422);
    }

    public function test_expired_at_at_at_user_apply_promos()
    {
        $promo = Promo::where('expired_at', '<', now())->first();

        $response = $this->post('/api/v1/apply-promo', [
            'code'          => $promo->code,
        ]);

        $response->assertStatus(422);
    }
}
