<?php

namespace Tests\Feature\Product;

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminProductMediaTest extends TestCase
{
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

    public function test_can_admin_store_product_images_2()
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

    public function test_can_admin_store_product_video()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('id', 2)->first();

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/videos', [
            'video'     => UploadedFile::fake()->create('video.mp4'),
            'thumbnail' => UploadedFile::fake()->image('thumbnail.png', 600, 600)
        ]);

        $response->assertStatus(201);
    }

    public function test_can_admin_store_product_video_2()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('id', 2)->first();

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/videos', [
            'video'     => UploadedFile::fake()->create('video.mp4'),
            'thumbnail' => UploadedFile::fake()->image('thumbnail.png', 600, 600)
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

    public function test_image_required_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'images'              => [
                0 => null
            ],
        ]);

        $response->assertSessionHasErrors('images.0');
    }

    public function test_image_id_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'images'              => [
                0 => '100000',
            ],
        ]);

        $response->assertSessionHasErrors('images.0');
    }

    public function test_deleted_image_required_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'deleted_images'              => [
                0 => null
            ],
        ]);

        $response->assertSessionHasErrors('deleted_images.0');
    }

    public function test_deleted_image_id_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'deleted_images'              => [
                0 => '100000',
            ],
        ]);

        $response->assertSessionHasErrors('deleted_images.0');
    }

    public function test_video_required_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'videos'              => [
                0 => null
            ],
        ]);

        $response->assertSessionHasErrors('videos.0');
    }

    public function test_video_id_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'videos'              => [
                0 => '100000',
            ],
        ]);

        $response->assertSessionHasErrors('videos.0');
    }

    public function test_deleted_video_nullable_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'deleted_videos'              => [
                0 => null
            ],
        ]);

        $response->assertSessionHasErrors('deleted_videos.0');
    }

    public function test_deleted_videos_id_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'deleted_videos'              => [
                0 => '100000',
            ],
        ]);

        $response->assertSessionHasErrors('deleted_videos.0');
    }

    public function test_tutorial_video_mime_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'tutorial_video' => UploadedFile::fake()->create('video.pdf'),
        ]);

        $response->assertSessionHasErrors('tutorial_video');
    }

    public function test_tutorial_video_size_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'tutorial_video' => UploadedFile::fake()->create('video.pdf', 10000),
        ]);

        $response->assertSessionHasErrors('tutorial_video');
    }

    public function test_tutorial_video_thumbnail_required_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'tutorial_video'           => UploadedFile::fake()->create('video.mp4'),
            'tutorial_video_thumbnail' => null,
        ]);

        $response->assertSessionHasErrors('tutorial_video_thumbnail');
    }

    public function test_tutorial_video_thumbnail_mime_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'tutorial_video' => UploadedFile::fake()->create('video.mp4'),
            'tutorial_video_thumbnail' => UploadedFile::fake()->image('thumbnail.pdf', 600, 600),
        ]);

        $response->assertSessionHasErrors('tutorial_video_thumbnail');
    }

    public function test_tutorial_video_thumbnail_size_at_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'tutorial_video' => UploadedFile::fake()->create('video.mp4'),
            'tutorial_video_thumbnail' => UploadedFile::fake()->image('thumbnail.pdf', 8000, 8000),
        ]);

        $response->assertSessionHasErrors('tutorial_video_thumbnail');
    }

    public function test_can_admin_save_product_media()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();
        $productImage       = ProductImage::where('product_id', $product->id)->where('is_active', 0)->first();
        $productVideo       = ProductVideo::where('product_id', $product->id)->where('is_active', 0)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'images'              => [
                0 => $productImage->id,
            ],
            // 'deleted_images' => [
            //     0 => $savedProductImage?->id
            // ],
            'videos'              => [
                0 => $productVideo->id,
            ],
            // 'deleted_videos' => [
            //     0 => $savedProductIVideo?->id
            // ],
            'tutorial_video'            => UploadedFile::fake()->create('video.mp4'),
            'tutorial_video_thumbnail'  => UploadedFile::fake()->image('thumbnail.png', 600, 600)
        ]);

        $response->assertStatus(201);
    }

    public function test_can_admin_save_product_media_2()
    {
        $faker = \Faker\Factory::create();

        $admin              = Admin::where('id', 1)->first();
        $product            = Product::whereHas('images')->whereHas('videos')->first();
        $productImage       = ProductImage::where('product_id', $product->id)->where('is_active', 0)->first(); 
        $savedProductImage  = ProductImage::where('product_id', $product->id)->where('is_active', 1)->first(); 
        $productVideo       = ProductVideo::where('product_id', $product->id)->where('is_active', 0)->first(); 
        $savedProductIVideo = ProductVideo::where('product_id', $product->id)->where('is_active', 1)->first(); 

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products/' . $product->slug . '/save-media', [
            'images'              => [
                0 => $productImage->id,
            ],
            'deleted_images' => [
                0 => $savedProductImage?->id
            ],
            'videos'              => [
                0 => $productVideo->id,
            ],
            'deleted_videos' => [
                0 => $savedProductIVideo?->id
            ],
            'tutorial_video'            => UploadedFile::fake()->create('video.mp4'),
            'tutorial_video_thumbnail'  => UploadedFile::fake()->image('thumbnail.png', 600, 600)
        ]);

        $response->assertStatus(201);
    }
}
