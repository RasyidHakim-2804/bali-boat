<?php

namespace Tests\Feature\Product;

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminProductVariantTest extends TestCase
{
    public function test_get_list_product_variant()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('is_variant', 1)->first();

        $response = $this->get('/api/v1/admins/products/' . $product->slug . '/variants');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'product_id',
                    'name',
                    'price',
                    'weight',
                    'height',
                    'width',
                    'lenght',
                    'stock',
                    'max_order',
                    'created_at',
                    'updated_at'
                ]
            ],
            'links',
            'meta'
        ]);
    }

    public function test_get_list_product_variant_of_unvaried_product()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('is_variant', 0)->first();

        $response = $this->get('/api/v1/admins/products/' . $product->slug . '/variants');

        $response->assertStatus(200);

        $response->assertJsonCount(0, 'data');
    }

    public function test_scope_binding_parameter_get_detail_product_variant()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('is_variant', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $response = $this->get('/api/v1/admins/products/100000000/variants/' . $variant->id);

        $response->assertStatus(404);
    }

    public function test_invalid_variant_parameter_get_detail_product_variant()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('is_variant', 1)->first();

        $variant = ProductVariant::where('product_id', 1)->first();

        $response = $this->get('/api/v1/products/'. $product->slug .'/variants/10000');

        $response->assertStatus(404);
    }

    public function test_get_detail_product()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('is_variant', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $response = $this->get('/api/v1/admins/products/' . $product->slug . '/variants/' . $variant->id);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                    'id',
                    'product_id',
                    'name',
                    'price',
                    'weight',
                    'height',
                    'width',
                    'lenght',
                    'stock',
                    'max_order',
                    'created_at',
                    'updated_at'
            ]
        ]);
    }

    public function test_can_admin_update_stock_variant_product()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('is_variant', 1)->first();
        $variant = ProductVariant::where('product_id', $product->id)->where('stock', '>', 0)->first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug . '/variants/' . $variant->id . '/stock', [
            'note'         => $faker->sentence(10),
            'type'         => 'out',
            'amount'       => 3,
        ]);

        $newVariant = ProductVariant::where('id', $variant->id)->first();

        $this->assertSame(($variant->stock - 3), $newVariant->stock);

        $response->assertStatus(201);
    }

    public function test_type_required_admin_update_stock_variant_product()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('is_variant', 1)->first();
        $variant = ProductVariant::where('product_id', $product->id)->where('stock', '>', 0)->first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug . '/variants/' . $variant->id . '/stock', [
            'note'         => $faker->sentence(10),
            'type'         => null,
            'amount'       => 3,
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_type_in_admin_update_stock_variant_product()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('is_variant', 1)->first();
        $variant = ProductVariant::where('product_id', $product->id)->where('stock', '>', 0)->first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug . '/variants/' . $variant->id . '/stock', [
            'note'         => $faker->sentence(10),
            'type'         => 'new',
            'amount'       => 3,
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_required_note_admin_update_stock_variant_product()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('is_variant', 1)->first();
        $variant = ProductVariant::where('product_id', $product->id)->where('stock', '>', 0)->first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug . '/variants/' . $variant->id . '/stock', [
            'note'         => null,
            'type'         => 'out',
            'amount'       => $faker->numberBetween(1, 99),
        ]);

        $response->assertSessionHasErrors('note');
    }

    public function test_required_amount_admin_update_stock_variant_product()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('is_variant', 1)->first();
        $variant = ProductVariant::where('product_id', $product->id)->where('stock', '>', 0)->first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug . '/variants/' . $variant->id . '/stock', [
            'note'         => $faker->sentence(10),
            'type'         => 'out',
            'amount'       => null,
        ]);

        $response->assertSessionHasErrors('amount');
    }
}
