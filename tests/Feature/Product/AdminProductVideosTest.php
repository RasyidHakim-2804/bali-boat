<?php

namespace Tests\Feature\Product;

use App\Models\Admin;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminProductVideosTest extends TestCase
{
    public function test_admin_get_list_product_video()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('id', 2)->first();

        $response = $this->get('/api/v1/admins/products/' . $product->slug . '/videos');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data',
        ]);
    }

    public function test_users_id_parameter_admin_get_detail_product_video()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/products/100000000');

        $response->assertStatus(404);
    }

    public function test_video_mime_at_admin_store_product_video()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('id', 2)->first();

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/videos', [
            'video' => UploadedFile::fake()->create('video.pdf')
        ]);

        $response->assertSessionHasErrors('video');
    }

    // public function test_video_size_at_admin_store_product_video()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $product = Product::where('id', 2)->first();

    //     $response = $this->post('/api/v1/admins/products/' . $product->slug . '/videos', [
    //         'video' => UploadedFile::fake()->create('video.mp4', 1)
    //     ]);

    //     $response->assertSessionHasErrors('video');
    // }

    public function test_video_thumbnail_required_at_admin_store_product_video()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('id', 2)->first();

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/videos', [
            'video'    => UploadedFile::fake()->create('video.mp4', 100),
            'thumbnail' => null
        ]);

        $response->assertSessionHasErrors('thumbnail');
    }

    public function test_video_thumbnail_mime_at_admin_store_product_video()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('id', 2)->first();

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/videos', [
            'video'    => UploadedFile::fake()->create('video.mp4', 100),
            'thumbnail' => UploadedFile::fake()->image('image.pdf', 600, 600)
        ]);

        $response->assertSessionHasErrors('thumbnail');
    }

    // public function test_video_thumbnail_size_at_admin_store_product_video()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $product = Product::where('id', 2)->first();

    //     $response = $this->post('/api/v1/admins/products/' . $product->slug . '/videos', [
    //         'video'    => UploadedFile::fake()->create('video.mp4', 100),
    //         'thumbnail' => UploadedFile::fake()->image('image.png', 8000, 8000)
    //     ]);

    //     $response->assertSessionHasErrors('thumbnail');
    // }

    public function test_admin_store_product_video()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('id', 2)->first();

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/videos', [
            'video' => UploadedFile::fake()->create('video.mp4', 100),
            'thumbnail' => UploadedFile::fake()->image('image.png', 200, 200)
        ]);

        $response->assertStatus(201);
    }

    // public function test_size_image_admin_store_product_video()
    // {
    //     $faker = \Faker\Factory::create();

    //     $admin = Admin::where('id', 1)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $product = Product::where('id', 2)->first();

    //     $response = $this->post('/api/v1/admins/products/' . $product->slug . '/videos', [
    //         'image' => UploadedFile::fake()->image('image.png', 6600, 6600)
    //     ]);

    //     $response->assertSessionHasErrors('image');
    // }

    // public function test_users_id_parameter_admin_save_product_video()
    // {
    //     $admin = Admin::where('id', 1)->first();

    //     $this->actingAs($admin, 'api_admin');

    //     $response = $this->post('/api/v1/admins/products/100000000/videos');

    //     $response->assertStatus(404);
    // }

    // public function test_can_admin_save_product_video()
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

    // public function test_required_images_admin_save_product_video()
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

    // public function test_exists_images_admin_save_product_video()
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

    // public function test_distinc_images_admin_save_product_video()
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

    // public function test_exists_deleted_images_admin_save_deletd_product_video()
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

    // public function test_distinc_deleted_images_admin_save_deletd_product_video()
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

    // public function test_can_admin_save_deletd_product_video()
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
