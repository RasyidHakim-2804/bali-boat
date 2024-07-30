<?php

namespace Tests\Feature\Product;

use App\Models\Admin;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PublicProductTest extends TestCase
{
    public function test_get_list_product()
    {
        $response = $this->get('/api/v1/products');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'price',
                    'weight',
                    'height',
                    'width',
                    'lenght',
                    'is_bundle',
                    'is_saleable',
                    'stock',
                    'image',
                    'created_at',
                    'updated_at'
                ]
            ],
            'links',
            'meta'
        ]);
    }

    // public function test_get_list_homepage_product()
    // {
    //     $response = $this->get('/api/v1/homepage-product-list');

    //     $response->assertStatus(200);

    //     $response->assertJsonStructure([
    //         'data' => [
    //             [
    //                 'id',
    //                 'name',
    //                 'slug',
    //                 'description',
    //                 'price',
    //                 'weight',
    //                 'height',
    //                 'width',
    //                 'lenght',
    //                 'is_bundle',
    //                 'is_saleable',
    //                 'stock',
    //                 'images',
    //                 'created_at',
    //                 'updated_at'
    //             ]
    //         ],
    //         'links',
    //         'meta'
    //     ]);
    // }

    public function test_product_id_parameter_get_detail_product()
    {
        $response = $this->get('/api/v1/products/100000000');

        $response->assertStatus(404);
    }

    public function test_get_detail_product()
    {
        $product = Product::first();

        $response = $this->get('/api/v1/products/' . $product->slug);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'name',
                'slug',
                'description',
                'full_description',
                'bundle_video',
                'bundle_video_thumbnail',
                'price',
                'weight',
                'height',
                'width',
                'lenght',
                'is_bundle',
                'is_saleable',
                'next_product',
                'next_step_title',
                'next_step_no',
                'stock',
                'ingredients',
                'images',
                'videos',
                'bundle_products',
                'variants',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_get_homepage_bundle_product()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/homepage-product');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'name',
                'slug',
                'description',
                'bundle_video',
                'bundle_video_thumbnail',
                'price',
                'weight',
                'height',
                'width',
                'lenght',
                'is_bundle',
                'is_saleable',
                'next_product',
                'next_step_title',
                'next_step_no',
                'stock',
                'ingredients',
                'images',
                'videos',
                'bundle_products',
                'variants',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_filter_price_at_product_list()
    {
        $minPrice = 0;
        $maxPrice = 3;

        $response = $this->get('/api/v1/products?filter[min_price]=' . $minPrice . '&filter[max_price]=' . $maxPrice);

        $totalProduct = (Product::where(function($query) use ($minPrice, $maxPrice) {
            $query->where('is_variant', 0)->where('price', '<=', $maxPrice)->where('price', '>=', $minPrice);
        })->orWhereHas('variants', function($variants) use($minPrice, $maxPrice) {
            $variants->where('stock', '!=', 0)->where('price', '<=', $maxPrice)->where('price', '>=', $minPrice);
        })->where('deleted_at', null)->count());

        $response->assertJsonCount($totalProduct, 'data');

        $response->assertStatus(200);
    }

    public function test_bundle_product_at_product_list()
    {
        $bundleProduct  = Product::where('is_bundle', 1)->first();
        $response       = $this->get('/api/v1/products?filter[bundle]=' . $bundleProduct->id);

        $totalProduct = Product::where('id', $bundleProduct->id)->orWhereHas('parentBundles', function($q) use($bundleProduct) {
            $q->where('bundle_id', $bundleProduct->id);
        })->where('deleted_at', null)->count();

        $response->assertJsonCount($totalProduct, 'data');

        $response->assertStatus(200);
    }

    public function test_sort_by_name_at_product_list()
    {
        $response = $this->get('/api/v1/products?sort=name');
        $product = Product::orderBy('name', 'ASC')->where('deleted_at', null)->where('is_saleable', 1)->where('stock', '>', 0)->first()->id;

        $this->assertSame($product, $response['data'][0]['id']);

        $response->assertStatus(200);
    }

    public function test_sort_by_lowest_price_at_product_list()
    {
        $select = DB::raw("(CASE WHEN products.is_variant='1' THEN (select price from product_variants where product_id = products.id and stock != 0 order by price asc limit 1) ELSE products.price END) as price");
        
        $response = $this->get('/api/v1/products?sort=price');
        $product = Product::orderBy('price', 'ASC')->select( 'products.*', $select)->where('deleted_at', null)->where('is_saleable', 1)->where('stock', '>', 0)->first()->id;

        $this->assertSame($product, $response['data'][0]['id']);

        $response->assertStatus(200);
    }

    public function test_sort_by_highest_price_at_product_list()
    {
        $select = DB::raw("(CASE WHEN products.is_variant='1' THEN (select price from product_variants where product_id = products.id and stock != 0 order by price asc limit 1) ELSE products.price END) as price");
        
        $response = $this->get('/api/v1/products?sort=-price');
        $product = Product::orderBy('price', 'DESC')->select( 'products.*', $select)->where('deleted_at', null)->where('is_saleable', 1)->where('stock', '>', 0)->first()->id;

        $this->assertSame($product, $response['data'][0]['id']);

        $response->assertStatus(200);
    }
}
