<?php

namespace Tests\Feature\Product;

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductAdditional;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    public function test_admin_get_list_product()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/products');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_users_id_parameter_admin_get_detail_product()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->get('/api/v1/admins/products/100000000');

        $response->assertStatus(404);
    }

    public function test_admin_get_detail_product()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->get('/api/v1/admins/products/' . $product->slug);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'name',
                'slug',
                'description',
                'price',
                'usd_price',
                'eur_price',
                'jpy_price',
                'cny_price',
                'is_saleable',
                'images',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_admin_store_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'type'          => 'activity',
            'is_varied'     => 0,
            'is_saleable'   => 1,
            'product_additionals'   => [
                0 => [
                    'additional_id' => 1
                ],
                1 => [
                    'additional_id' => 2
                ]
            ]
        ]);

        $response->assertStatus(201);
    }


    public function test_admin_store_product_with_null_additional()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'type'          => 'activity',
            'is_varied'     => 0,
            'is_saleable'   => 1,
            'product_additionals'   => [
                0 => [
                ]
            ]
        ]);

        $response->assertSessionHasErrors('product_additionals.0.additional_id');
    }

    public function test_admin_store_product_with_invalid_additional()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'type'          => 'activity',
            'is_varied'     => 0,
            'is_saleable'   => 1,
            'product_additionals'   => [
                0 => [
                    'additional_id' => 100000,
                ]
            ]
        ]);

        $response->assertSessionHasErrors('product_additionals.0.additional_id');
    }

    public function test_admin_store_varied_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'type'          => 'activity',
            'product_additionals'   => [
                0 => [
                    'additional_id' => 1
                ],
                1 => [
                    'additional_id' => 2
                ]
            ],
            'product_variants'     => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 6),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                    'product_additionals'   => [
                        0 => [
                            'additional_id' => 3
                        ],
                        1 => [
                            'additional_id' => 4
                        ]
                    ],
                ],
                1 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ]
            ],
        ]);

        $response->assertStatus(201);
    }

    public function test_admin_update_varied_product_with_additional()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $productAdditional  = ProductAdditional::where('variant_id', '!=', null)->first();
        $productVariant     = ProductVariant::where('id', $productAdditional->variant_id)->first();
        $product            = Product::where('id', $productAdditional->product_id)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'type'          => 'activity',
            'product_additionals'   => [
                0 => [
                    'additional_id' => 1
                ],
                1 => [
                    'additional_id' => 2
                ]
            ],
            'product_variants'     => [
                0 => [
                    'id'            => $productVariant->id,
                    'name'          => $productVariant->name,
                    'price'         => $productVariant->price,
                    'location_id'   => $productVariant->location_id,
                    'usd_price'     => $productVariant->usd_price,
                    'jpy_price'     => $productVariant->jpy_price,
                    'cny_price'     => $productVariant->cny_price,
                    'eur_price'     => $productVariant->eur_price,
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                    'product_additionals'   => [
                        0 => [
                            'id'            => $productAdditional->id,
                            'additional_id' => 3
                        ],
                        1 => [
                            'additional_id' => 4
                        ]
                    ],
                ],
                1 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ]
            ],
        ]);

        $response->assertStatus(201);
    }

    public function test_admin_update_varied_product_with_invalid_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $productAdditional  = ProductAdditional::where('variant_id', '!=', null)->first();
        $productVariant     = ProductVariant::where('id', $productAdditional->variant_id)->first();
        $product            = Product::where('id', $productAdditional->product_id)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'type'          => 'activity',
            'product_additionals'   => [
                0 => [
                    'additional_id' => $faker->numberBetween(1, 6)
                ],
                1 => [
                    'additional_id' => $faker->numberBetween(1, 6)
                ]
            ],
            'product_variants'     => [
                0 => [
                    'id'            => 100000,
                    'name'          => $productVariant->name,
                    'price'         => $productVariant->price,
                    'location_id'   => $productVariant->location_id,
                    'usd_price'     => $productVariant->usd_price,
                    'jpy_price'     => $productVariant->jpy_price,
                    'cny_price'     => $productVariant->cny_price,
                    'eur_price'     => $productVariant->eur_price,
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                    'product_additionals'   => [
                        0 => [
                            'id'            => $productAdditional->id,
                            'additional_id' => $faker->numberBetween(1, 6)
                        ],
                        1 => [
                            'additional_id' => $faker->numberBetween(1, 6)
                        ]
                    ],
                ],
                1 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ]
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.id');
    }

    public function test_admin_store_varied_product_with_null_additional()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'type'          => 'activity',
            'product_additionals'   => [
                0 => [
                    'additional_id' => $faker->numberBetween(1, 6)
                ],
                1 => [
                    'additional_id' => $faker->numberBetween(1, 6)
                ]
            ],
            'product_variants'     => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 6),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                    'product_additionals'   => [
                        0 => []
                    ],
                ],
                1 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ]
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.product_additionals.0.additional_id');
    }

    public function test_admin_store_varied_product_with_invalid_additional()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'type'          => 'activity',
            'product_additionals'   => [
                0 => [
                    'additional_id' => $faker->numberBetween(1, 6)
                ],
                1 => [
                    'additional_id' => $faker->numberBetween(1, 6)
                ]
            ],
            'product_variants'     => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 6),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                    'product_additionals'   => [
                        0 => [
                            'additional_id' => 100000
                        ]
                    ],
                ],
                1 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ]
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.product_additionals.0.additional_id');
    }

    public function test_usd_price_required_admin_store_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => null,
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'type'          => 'activity',
            'is_varied'     => 0,
            'is_saleable'   => 1,
            
        ]);

        $response->assertSessionHasErrors('usd_price');
    }

    public function test_usd_price_numeric_admin_store_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => 'null',
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'type'          => 'activity',
            'is_varied'     => 0,
            'is_saleable'   => 1,
            
        ]);

        $response->assertSessionHasErrors('usd_price');
    }

    public function test_eur_price_required_admin_store_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 8),
            'eur_price'     => null,
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'type'          => 'activity',
            'is_varied'     => 0,
            'is_saleable'   => 1,
            
        ]);

        $response->assertSessionHasErrors('eur_price');
    }

    public function test_eur_price_numeric_admin_store_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => 'null',
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'type'          => 'activity',
            'is_varied'     => 0,
            'is_saleable'   => 1,
            
        ]);

        $response->assertSessionHasErrors('eur_price');
    }

    public function test_jpy_price_required_admin_store_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 8),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => null,
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'type'          => 'activity',
            'is_varied'     => 0,
            'is_saleable'   => 1,
        ]);

        $response->assertSessionHasErrors('jpy_price');
    }

    public function test_jpy_price_numeric_admin_store_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => 'null',
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'type'          => 'activity',
            'is_varied'     => 0,
            'is_saleable'   => 1,
        ]);

        $response->assertSessionHasErrors('jpy_price');
    }

    public function test_cny_price_required_admin_store_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 8),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => null,
            'eur_price'     => $faker->randomFloat(2, 1, 8),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'type'          => 'activity',
            'is_varied'     => 0,
            'is_saleable'   => 1,
        ]);

        $response->assertSessionHasErrors('cny_price');
    }

    public function test_cny_price_numeric_admin_store_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->post('/api/v1/admins/products', [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => 'null',
            'type'          => 'activity',
            'is_varied'     => 0,
            'is_saleable'   => 1,
            
        ]);

        $response->assertSessionHasErrors('cny_price');
    }

    public function test_users_id_parameter_admin_update_product()
    {
        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/products/100000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $product = Product::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 0,
            'is_saleable'   => 1,
        ]);

        $response->assertStatus(201);
    }

    public function test_required_name_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => null,
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(null, 0, 6),
            'eur_price'     => $faker->randomFloat(null, 0, 6),
            'jpy_price'     => $faker->randomFloat(null, 0, 6),
            'cny_price'     => $faker->randomFloat(null, 0, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 0,
            'is_saleable'   => 1,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_boolean_in_is_saleable_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(null, 0, 6),
            'eur_price'     => $faker->randomFloat(null, 0, 6),
            'jpy_price'     => $faker->randomFloat(null, 0, 6),
            'cny_price'     => $faker->randomFloat(null, 0, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 0,
            'is_saleable'   => 'test',
            
        ]);

        $response->assertSessionHasErrors('is_saleable');
    }

    public function test_required_description_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => null,
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(null, 0, 6),
            'eur_price'     => $faker->randomFloat(null, 0, 6),
            'jpy_price'     => $faker->randomFloat(null, 0, 6),
            'cny_price'     => $faker->randomFloat(null, 0, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 0,
            'is_saleable'   => 1,
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_required_price_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'              => $faker->sentence(3),
            'description'       => $faker->sentence(10),
            'type'              => 'activity',
            'price'             => null,
            'usd_price'         => $faker->randomFloat(null, 0, 6),
            'eur_price'         => $faker->randomFloat(null, 0, 6),
            'jpy_price'         => $faker->randomFloat(null, 0, 6),
            'cny_price'         => $faker->randomFloat(null, 0, 6),
            'max_order'         => null,
            'min_order'         => 1,
            'person_qty'        => 1,
            'is_varied'         => 0,
            'is_saleable'       => 1,
        ]);

        $response->assertSessionHasErrors('price');
    }

    public function test_numeric_price_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'              => $faker->sentence(3),
            'description'       => $faker->sentence(10),
            'type'              => 'activity',
            'price'             => 'est',
            'usd_price'         => $faker->randomFloat(null, 0, 6),
            'eur_price'         => $faker->randomFloat(null, 0, 6),
            'jpy_price'         => $faker->randomFloat(null, 0, 6),
            'cny_price'         => $faker->randomFloat(null, 0, 6),
            'max_order'         => null,
            'min_order'         => 1,
            'person_qty'        => 1,
            'is_varied'         => 0,
            'is_saleable'       => 1,
        ]);

        $response->assertSessionHasErrors('price');
    }

    public function test_required_usd_price_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => null,
            'eur_price'     => $faker->randomFloat(null, 0, 6),
            'jpy_price'     => $faker->randomFloat(null, 0, 6),
            'cny_price'     => $faker->randomFloat(null, 0, 6),
            'max_order'         => null,
            'min_order'         => 1,
            'person_qty'        => 1,
            'is_varied'     => 0,
            'is_saleable'   => 1,
        ]);

        $response->assertSessionHasErrors('usd_price');
    }

    public function test_numeric_usd_price_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => 'null',
            'eur_price'     => $faker->randomFloat(null, 0, 6),
            'jpy_price'     => $faker->randomFloat(null, 0, 6),
            'cny_price'     => $faker->randomFloat(null, 0, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 0,
            'is_saleable'   => 1,
        ]);

        $response->assertSessionHasErrors('usd_price');
    }

    public function test_required_eur_price_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'              => $faker->sentence(3),
            'description'       => $faker->sentence(10),
            'type'              => 'activity',
            'price'             => $faker->randomFloat(null, 0, 6),
            'usd_price'         => $faker->randomFloat(null, 0, 6),
            'eur_price'         => null,
            'jpy_price'         => $faker->randomFloat(null, 0, 6),
            'cny_price'         => $faker->randomFloat(null, 0, 6),
            'max_order'         => null,
            'min_order'         => 1,
            'person_qty'        => 1,
            'is_varied'         => 0,
            'is_saleable'       => 1,
        ]);

        $response->assertSessionHasErrors('eur_price');
    }

    public function test_numeric_eur_price_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'              => $faker->sentence(3),
            'description'       => $faker->sentence(10),
            'type'          => 'activity',
            'price'             => $faker->randomFloat(null, 0, 6),
            'usd_price'         => $faker->randomFloat(null, 0, 6),
            'eur_price'         => 'est',
            'jpy_price'         => $faker->randomFloat(null, 0, 6),
            'cny_price'         => $faker->randomFloat(null, 0, 6),
            'max_order'         => null,
            'min_order'         => 1,
            'person_qty'        => 1,
            'is_varied'         => 0,
            'is_saleable'       => 1,
        ]);

        $response->assertSessionHasErrors('eur_price');
    }

    public function test_required_Jpy_price_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'              => $faker->sentence(3),
            'description'       => $faker->sentence(10),
            'type'          => 'activity',
            'price'             => $faker->randomFloat(null, 0, 6),
            'usd_price'         => $faker->randomFloat(null, 0, 6),
            'eur_price'         => $faker->randomFloat(null, 0, 6),
            'jpy_price'         => null,
            'cny_price'         => $faker->randomFloat(null, 0, 6),
            'max_order'         => null,
            'min_order'         => 1,
            'person_qty'        => 1,
            'is_varied'         => 0,
            'is_saleable'       => 1,
        ]);

        $response->assertSessionHasErrors('jpy_price');
    }

    public function test_numeric_jpy_price_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'              => $faker->sentence(3),
            'description'       => $faker->sentence(10),
            'type'          => 'activity',
            'price'             => $faker->randomFloat(null, 0, 6),
            'usd_price'         => $faker->randomFloat(null, 0, 6),
            'eur_price'         => $faker->randomFloat(null, 0, 6),
            'jpy_price'         => 'test',
            'cny_price'         => $faker->randomFloat(null, 0, 6),
            'max_order'         => null,
            'min_order'         => 1,
            'person_qty'        => 1,
            'is_varied'         => 0,
            'is_saleable'       => 1,
        ]);

        $response->assertSessionHasErrors('jpy_price');
    }

    public function test_required_cny_price_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'              => $faker->sentence(3),
            'description'       => $faker->sentence(10),
            'type'          => 'activity',
            'price'             => $faker->randomFloat(null, 0, 6),
            'usd_price'         => $faker->randomFloat(null, 0, 6),
            'eur_price'         => $faker->randomFloat(null, 0, 6),
            'jpy_price'         => $faker->randomFloat(null, 0, 6),
            'cny_price'         => null,
            'is_varied'         => 0,
            'is_saleable'       => 1,
        ]);

        $response->assertSessionHasErrors('cny_price');
    }

    public function test_numeric_cny_price_at_admin_update_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'              => $faker->sentence(3),
            'description'       => $faker->sentence(10),
            'type'          => 'activity',
            'price'             => $faker->randomFloat(null, 0, 6),
            'usd_price'         => $faker->randomFloat(null, 0, 6),
            'eur_price'         => $faker->randomFloat(null, 0, 6),
            'jpy_price'         => $faker->randomFloat(null, 0, 6),
            'cny_price'         => 'test',
            'is_varied'         => 0,
            'is_saleable'       => 1,
        ]);

        $response->assertSessionHasErrors('cny_price');
    }

    public function test_admin_can_update_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
                1 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ]
            ],
        ]);

        $response->assertStatus(201);
    }

    public function test_admin_update_nullable_price_at_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => null,
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.price');
    }

    public function test_admin_update_invalid_price_at_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => 'test',
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.price');
    }

    public function test_admin_update_nullable_usd_price_at_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => null,
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.usd_price');
    }

    public function test_admin_update_invalid_usd_price_at_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => 'test',
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.usd_price');
    }

    public function test_admin_update_nullable_eur_price_at_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => null,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.eur_price');
    }

    public function test_admin_update_invalid_eur_price_at_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => 'test',
                ],
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.eur_price');
    }

    public function test_admin_update_nullable_jpy_price_at_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => null,
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.jpy_price');
    }

    public function test_admin_update_invalid_jpy_price_at_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => 'test',
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.jpy_price');
    }

    public function test_admin_update_nullable_cny_price_at_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => null,
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.cny_price');
    }

    public function test_admin_update_invalid_cny_price_at_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => 'test',
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.cny_price');
    }

    public function test_admin_update_nullable_location_Id_at_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => null,
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.location_id');
    }

    public function test_admin_update_invalid_location_id_at_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => 100000,
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('product_variants.0.location_id');
    }

    public function test_admin_can_update_variant_product_with_variant()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('is_varied', 1)->first();

        $productVariant = ProductVariant::where('product_id', $product->id)->first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'is_varied'     => 1,
            'is_saleable'   => 1,
            'product_variants'      => [
                0 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
                1 => [
                    'name'          => $faker->word(),
                    'price'         => $faker->randomFloat(2, 1, 8),
                    'location_id'   => $faker->numberBetween(1, 8),
                    'usd_price'     => $faker->randomFloat(2, 1, 8),
                    'jpy_price'     => $faker->randomFloat(2, 1, 8),
                    'cny_price'     => $faker->randomFloat(2, 1, 8),
                    'eur_price'     => $faker->randomFloat(2, 1, 8),
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ],
                2 => [
                    'id'            => $productVariant->id,
                    'name'          => $productVariant->name,
                    'location_id'   => $productVariant->location_id,
                    'price'         => $productVariant->price,
                    'usd_price'     => $productVariant->usd_price,
                    'jpy_price'     => $productVariant->jpy_price,
                    'cny_price'     => $productVariant->cny_price,
                    'eur_price'     => $productVariant->eur_price,
                    'max_order'     => null,
                    'min_order'     => 1,
                    'person_qty'    => 1,
            'is_need_confirmation'  => 1,
                ]
            ],
        ]);

        $response->assertStatus(201);
    }

    public function test_admin_can_update_varied_product_to_unvaried_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $product = Product::where('is_varied', 1)->first();

        $response = $this->put('/api/v1/admins/products/' . $product->slug, [
            'name'          => $faker->sentence(3),
            'description'   => $faker->sentence(10),
            'type'          => 'activity',
            'price'         => $faker->randomFloat(2, 1, 8),
            'usd_price'     => $faker->randomFloat(2, 1, 6),
            'eur_price'     => $faker->randomFloat(2, 1, 6),
            'jpy_price'     => $faker->randomFloat(2, 1, 6),
            'cny_price'     => $faker->randomFloat(2, 1, 6),
            'max_order'     => null,
            'min_order'     => 1,
            'person_qty'    => 1,
            'is_need_confirmation'  => 1,
            'is_varied'     => 0,
            'is_saleable'   => 1,
        ]);

        $response->assertStatus(201);
    }

    public function test_users_id_parameter_admin_delete_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/products/10000000');

        $response->assertStatus(404);
    }

    public function test_can_admin_delete_product()
    {
        $faker = \Faker\Factory::create();

        $admin = Admin::where('id', 1)->first();

        $product = Product::orderBy('id', 'DESC')->first();

        $this->actingAs($admin, 'api_admin');

        $response = $this->delete('/api/v1/admins/products/'. $product->slug);

        $response->assertStatus(201);
    }
}
