<?php

namespace Tests\Feature\Product;

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminProductImagesTest extends TestCase
{
    public function test_admin_get_list_product_images()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('id', 2)->first();

        $response = $this->get('/api/v1/admins/products/' . $product->slug . '/images');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data',
        ]);
    }

    public function test_users_id_parameter_admin_get_detail_product_images()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/products/100000000');

        $response->assertStatus(404);
    }

    public function test_mime_image_admin_store_product_images()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('id', 2)->first();

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/images', [
            'image' => UploadedFile::fake()->image('image.pdf', 600, 600)
        ]);

        $response->assertSessionHasErrors('image');
    }

    public function test_can_admin_store_product_images()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('id', 2)->first();

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/images', [
            'image' => UploadedFile::fake()->image('image.png', 600, 600)
        ]);

        $response->assertStatus(201);
    }

    // public function test_size_image_admin_store_product_images()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $product = Product::where('id', 2)->first();

    //     $response = $this->post('/api/v1/admins/products/' . $product->slug . '/images', [
    //         'image' => UploadedFile::fake()->image('image.png', 6600, 6600)
    //     ]);

    //     $response->assertSessionHasErrors('image');
    // }

    // public function test_users_id_parameter_admin_save_product_images()
    // {
    //     $admin = Admin::where('id', 1)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $response = $this->post('/api/v1/admins/products/100000000/images');

    //     $response->assertStatus(404);
    // }

    // public function test_can_admin_save_product_images()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $product = Product::where('id', 1)->first();

    //     $productImage = ProductImage::where('id', 1)->where('is_active', 0)->first(); 

    //     $this->actingAs($admin, 'api_admin');

    //     $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-images', [
    //         'images'              => [
    //             0 => $productImage->id,
    //         ],
    //     ]);

    //     $response->assertStatus(201);
    // }

    // public function test_required_images_admin_save_product_images()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $product = Product::where('id', 1)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-images', [
    //         'images'              => null,
    //     ]);

    //     $response->assertSessionHasErrors('images');
    // }

    // public function test_exists_images_admin_save_product_images()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $product = Product::where('id', 2)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-images', [
    //         'images'              => [
    //             0 => 1000000
    //         ],
    //     ]);

    //     $response->assertSessionHasErrors('images.*');
    // }

    // public function test_distinc_images_admin_save_product_images()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $product = Product::where('id', 2)->first();

    //     $productImage = ProductImage::where('product_id', $product->id)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-images', [
    //         'images'              => [
    //             0 => $productImage->id,
    //             1 => $productImage->id
    //         ]
    //     ]);

    //     $response->assertSessionHasErrors('images.*');
    // }

    // public function test_exists_deleted_images_admin_save_deletd_product_images()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $product = Product::where('id', 2)->first();

    //     $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-images', [
    //         'deleted_images'     => [
    //             0 => 100000000
    //         ]
    //     ]);

    //     $response->assertSessionHasErrors('deleted_images.*');
    // }

    // public function test_distinc_deleted_images_admin_save_deletd_product_images()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $product = Product::where('id', 2)->first();

    //     $productImage = ProductImage::where('product_id', $product->id)->first();

    //     $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-images', [
    //         'deleted_images'     => [
    //             0 => $productImage->id,
    //             1 => $productImage->id
    //         ]
    //     ]);

    //     $response->assertSessionHasErrors('deleted_images.*');
    // }

    // public function test_can_admin_save_deletd_product_images()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $product = Product::where('id', 2)->first();

    //     $productImage = ProductImage::where('product_id', $product->id)->first();

    //     $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-images', [
    //         'deleted_images'     => [
    //             0 => $productImage->id
    //         ]
    //     ]);

    //     $response->assertStatus(201);
    // }
}
