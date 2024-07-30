<?php

namespace Tests\Feature\Order;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductAdditional;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UserOrderTest extends TestCase
{
    public function test_users_get_list_orders()
    {
        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->get('/api/v1/users/' . $user->uuid . '/orders');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_users_id_parameter_users_get_detail_orders()
    {
        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->get('/api/v1/users/' . $user->uuid . '/orders/100000000');

        $response->assertStatus(404);
    }

    public function test_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response2 = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'      => [
                [
                    'id'            => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => 2
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 2
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ],
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $newOrder               = Order::where('user_id', $user->id)->latest()->first();
        $totalPriceProduct      = $variant->price * 2;
        $totalUsdPriceProduct   = $variant->usd_price * 2;
        $totalEurPriceProduct   = $variant->eur_price * 2;
        $totalJpyPriceProduct   = $variant->jpy_price * 2;
        $totalCnyPriceProduct   = $variant->cny_price * 2;
        
        $totalPriceAdditional       = $productAdditional->additionalData->price * 2;
        $totalUsdPriceAdditional    = $productAdditional->additionalData->usd_price * 2;
        $totalEurPriceAdditional    = $productAdditional->additionalData->eur_price * 2;
        $totalJpyPriceAdditional    = $productAdditional->additionalData->jpy_price * 2;
        $totalCnyPriceAdditional    = $productAdditional->additionalData->cny_price * 2;

        $this->assertEquals($totalPriceProduct, $newOrder->total_purchased);
        $this->assertEquals($totalUsdPriceProduct, $newOrder->total_usd_purchased);
        $this->assertEquals($totalEurPriceProduct, $newOrder->total_eur_purchased);
        $this->assertEquals($totalJpyPriceProduct, $newOrder->total_jpy_purchased);
        $this->assertEquals($totalCnyPriceProduct, $newOrder->total_cny_purchased);

        $this->assertEquals($totalPriceAdditional, $newOrder->total_additional_purchased);
        $this->assertEquals($totalUsdPriceAdditional, $newOrder->total_usd_additional_purchased);
        $this->assertEquals($totalEurPriceAdditional, $newOrder->total_eur_additional_purchased);
        $this->assertEquals($totalJpyPriceAdditional, $newOrder->total_jpy_additional_purchased);
        $this->assertEquals($totalCnyPriceAdditional, $newOrder->total_cny_additional_purchased);

        $this->assertEquals(($totalPriceAdditional + $totalPriceProduct), $newOrder->grand_total_purchased);
        $this->assertEquals(($totalUsdPriceAdditional + $totalUsdPriceProduct), $newOrder->grand_total_usd_purchased);
        $this->assertEquals(($totalEurPriceAdditional + $totalEurPriceProduct), $newOrder->grand_total_eur_purchased);
        $this->assertEquals(($totalJpyPriceAdditional + $totalJpyPriceProduct), $newOrder->grand_total_jpy_purchased);
        $this->assertEquals(($totalCnyPriceAdditional + $totalCnyPriceProduct), $newOrder->grand_total_cny_purchased);

        $this->assertSame(0, $newOrder->is_instant_booking);

        $response2->assertStatus(201);
    }

    public function test_need_confirmation_2day_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->where('is_need_confirmation', 1)->where('min_confirmation_days', '!=', null)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response2 = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => date('Y-m-d', strtotime(now()->addDay())),
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'      => [
                [
                    'id'            => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => 2
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 2
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ],
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $newOrder               = Order::where('user_id', $user->id)->latest()->first();
        $totalPriceProduct      = $variant->price * 2;
        $totalUsdPriceProduct   = $variant->usd_price * 2;
        $totalEurPriceProduct   = $variant->eur_price * 2;
        $totalJpyPriceProduct   = $variant->jpy_price * 2;
        $totalCnyPriceProduct   = $variant->cny_price * 2;
        
        $totalPriceAdditional       = $productAdditional->additionalData->price * 2;
        $totalUsdPriceAdditional    = $productAdditional->additionalData->usd_price * 2;
        $totalEurPriceAdditional    = $productAdditional->additionalData->eur_price * 2;
        $totalJpyPriceAdditional    = $productAdditional->additionalData->jpy_price * 2;
        $totalCnyPriceAdditional    = $productAdditional->additionalData->cny_price * 2;

        $this->assertEquals($totalPriceProduct, $newOrder->total_purchased);
        $this->assertEquals($totalUsdPriceProduct, $newOrder->total_usd_purchased);
        $this->assertEquals($totalEurPriceProduct, $newOrder->total_eur_purchased);
        $this->assertEquals($totalJpyPriceProduct, $newOrder->total_jpy_purchased);
        $this->assertEquals($totalCnyPriceProduct, $newOrder->total_cny_purchased);

        $this->assertEquals($totalPriceAdditional, $newOrder->total_additional_purchased);
        $this->assertEquals($totalUsdPriceAdditional, $newOrder->total_usd_additional_purchased);
        $this->assertEquals($totalEurPriceAdditional, $newOrder->total_eur_additional_purchased);
        $this->assertEquals($totalJpyPriceAdditional, $newOrder->total_jpy_additional_purchased);
        $this->assertEquals($totalCnyPriceAdditional, $newOrder->total_cny_additional_purchased);

        $this->assertEquals(($totalPriceAdditional + $totalPriceProduct), $newOrder->grand_total_purchased);
        $this->assertEquals(($totalUsdPriceAdditional + $totalUsdPriceProduct), $newOrder->grand_total_usd_purchased);
        $this->assertEquals(($totalEurPriceAdditional + $totalEurPriceProduct), $newOrder->grand_total_eur_purchased);
        $this->assertEquals(($totalJpyPriceAdditional + $totalJpyPriceProduct), $newOrder->grand_total_jpy_purchased);
        $this->assertEquals(($totalCnyPriceAdditional + $totalCnyPriceProduct), $newOrder->grand_total_cny_purchased);

        $this->assertSame(1, $newOrder->is_instant_booking);

        $response2->assertStatus(201);
    }

    public function test_need_confirmation_always_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 1)->first();
        $variant = ProductVariant::where('product_id', $product->id)->where('is_need_confirmation', 1)->where('min_confirmation_days', null)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response2 = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'      => [
                [
                    'id'            => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => 2
                ]
            ],
            // 'additionals' => [
            //     [
            //         'id'        => $productAdditional->id,
            //         'quantity'  => 2
            //     ]
            // ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ],
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $newOrder               = Order::where('user_id', $user->id)->latest()->first();
        // $totalPriceProduct      = $variant->price * 2;
        // $totalUsdPriceProduct   = $variant->usd_price * 2;
        // $totalEurPriceProduct   = $variant->eur_price * 2;
        // $totalJpyPriceProduct   = $variant->jpy_price * 2;
        // $totalCnyPriceProduct   = $variant->cny_price * 2;
        
        // $totalPriceAdditional       = $productAdditional->additionalData->price * 2;
        // $totalUsdPriceAdditional    = $productAdditional->additionalData->usd_price * 2;
        // $totalEurPriceAdditional    = $productAdditional->additionalData->eur_price * 2;
        // $totalJpyPriceAdditional    = $productAdditional->additionalData->jpy_price * 2;
        // $totalCnyPriceAdditional    = $productAdditional->additionalData->cny_price * 2;

        // $this->assertEquals($totalPriceProduct, $newOrder->total_purchased);
        // $this->assertEquals($totalUsdPriceProduct, $newOrder->total_usd_purchased);
        // $this->assertEquals($totalEurPriceProduct, $newOrder->total_eur_purchased);
        // $this->assertEquals($totalJpyPriceProduct, $newOrder->total_jpy_purchased);
        // $this->assertEquals($totalCnyPriceProduct, $newOrder->total_cny_purchased);

        // $this->assertEquals($totalPriceAdditional, $newOrder->total_additional_purchased);
        // $this->assertEquals($totalUsdPriceAdditional, $newOrder->total_usd_additional_purchased);
        // $this->assertEquals($totalEurPriceAdditional, $newOrder->total_eur_additional_purchased);
        // $this->assertEquals($totalJpyPriceAdditional, $newOrder->total_jpy_additional_purchased);
        // $this->assertEquals($totalCnyPriceAdditional, $newOrder->total_cny_additional_purchased);

        // $this->assertEquals(($totalPriceAdditional + $totalPriceProduct), $newOrder->grand_total_purchased);
        // $this->assertEquals(($totalUsdPriceAdditional + $totalUsdPriceProduct), $newOrder->grand_total_usd_purchased);
        // $this->assertEquals(($totalEurPriceAdditional + $totalEurPriceProduct), $newOrder->grand_total_eur_purchased);
        // $this->assertEquals(($totalJpyPriceAdditional + $totalJpyPriceProduct), $newOrder->grand_total_jpy_purchased);
        // $this->assertEquals(($totalCnyPriceAdditional + $totalCnyPriceProduct), $newOrder->grand_total_cny_purchased);

        $this->assertSame(1, $newOrder->is_instant_booking);

        $response2->assertStatus(201);
    }

    public function test_users_store_orders_with_null_additional()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response2 = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'      => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response2->assertStatus(201);
    }

    public function test_users_store_orders_with_not_saleable_product()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->where('is_saleable', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'      => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.id');
    }

    public function test_additionals_id_required_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'      => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => null,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('additionals.0.id');
    }

    public function test_additionals_id_invalid_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'      => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => 10000,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('additionals.0.id');
    }

    public function test_additionals_quantity_required_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'      => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => null
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('additionals.0.quantity');
    }

    public function test_additionals_quantity_invalid_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 0
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('additionals.0.quantity');
    }

    public function test_forms_required_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
        ]);

        $response->assertSessionHasErrors('forms');
    }

    public function test_forms_product_id_required_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => null,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.product_id');
    }

    public function test_forms_product_id_invalid_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => 1000,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.product_id');
    }

    public function test_forms_product_name_required_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => null,
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.name');
    }

    public function test_forms_date_of_birth_required_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => null,
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.date_of_birth');
    }

    public function test_forms_date_of_birth_invalid_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => 'test',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.date_of_birth');
    }

    public function test_forms_email_required_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => null,
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.email');
    }

    public function test_forms_email_invalid_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.email');
    }

    public function test_forms_phone_required_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => null,
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.phone');
    }

    public function test_forms_is_pic_required_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => null
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.is_pic');
    }

    public function test_forms_is_pic_invalid_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 'null'
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.is_pic');
    }

    public function test_forms_gender_required_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => null,
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.gender');
    }

    public function test_forms_gender_invalid_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'null',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.gender');
    }

    public function test_forms_nationality_required_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => null,
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.nationality');
    }

    public function test_forms_pickup_address_nullable_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => null,
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_forms_diet_restriction_nullable_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => null,
                    'diet_restriction'          => null,
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_shoes_size_nullable_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => null,
                    'diet_restriction'          => null,
                    'shoes_size'                => null,
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_diving_association_number_nullable_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => null,
                    'diet_restriction'          => null,
                    'shoes_size'                => null,
                    'diving_association_number' => null,
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_last_dive_nullable_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => null,
                    'diet_restriction'          => null,
                    'shoes_size'                => null,
                    'diving_association_number' => null,
                    'last_dive'                 => null,
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_last_dive_invalid_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => null,
                    'diet_restriction'          => null,
                    'shoes_size'                => null,
                    'diving_association_number' => null,
                    'last_dive'                 => 'null',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.last_dive');
    }

    public function test_forms_quantity_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'      => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 2
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertStatus(422);
    }

    // // public function test_fixed_promo_apply_at_users_store_orders()
    // // {
    // //     $faker = \Faker\Factory::create();

    // //     $user = USer::where('is_active', 1)->first();

    // //     $this->actingAs($user, 'api_user');

    // //     $product = Product::where('is_varied', 0)->first();
    // //    $variant = ProductVariant::where('product_id', $product->id)->first();
    // //     $promo   = Promo::where('type', 'fixed')->first();

    // //     $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
    // //         'address_id'                => 1,
    // //         'promo_id'                  => $promo->id,
    // //         'delivery_provider'         => 'USPS',
    // //         'delivery_service'          => 'Priority 1 day',
    // //         'delivery_estimate_day'     => '1-3',
    // //         'delivery_fee'              => 123.43,
    // //         'products'                  => [
    // //             [
    // //                 'id'       => $product->id,
    // //                 'variant_id'    => $variant->id,
    // //                 'quantity' => 1
    // //             ]
    // //         ],
    // //     ]);

    // //     $response->assertStatus(201);
    // // }

    // // public function test_fixed_promo_uqual_to_total_purchased_at_users_store_orders()
    // // {
    // //     $faker = \Faker\Factory::create();

    // //     $user = USer::where('is_active', 1)->first();

    // //     $this->actingAs($user, 'api_user');

    // //     $product = Product::where('is_varied', 0)->first();
    // //    $variant = ProductVariant::where('product_id', $product->id)->first();
    // //     $promo   = Promo::where('type', 'fixed')->first();

    // //     $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
    // //         'address_id'                => 1,
    // //         'promo_id'                  => $promo->id,
    // //         'delivery_provider'         => 'USPS',
    // //         'delivery_service'          => 'Priority 1 day',
    // //         'delivery_estimate_day'     => '1-3',
    // //         'delivery_fee'              => 123.43,
    // //         'products'                  => [
    // //             [
    // //                 'id'       => $product->id,
    // //                'variant_id'    => $variant->id,
    // //                 'quantity' => 1
    // //             ]
    // //         ],
    // //     ]);

    // //     $order = Order::orderBy('id', 'DESC')->where('promo_id', $promo->id)->first();

    // //     $totalPurchasedDiscounted   = $order->total_purchased - $promo->amount;
    // //     $reCalculateGrandTotal      = $order->delivery_fee + $totalPurchasedDiscounted;

    // //     $this->assertSame(number_format($reCalculateGrandTotal, 2), $order->grand_total_purchased);

    // //     $response->assertStatus(201);
    // // }

    // // public function test_percentage_promo_apply_at_users_store_orders()
    // // {
    // //     $faker = \Faker\Factory::create();

    // //     $user = USer::where('is_active', 1)->first();

    // //     $this->actingAs($user, 'api_user');

    // //     $product = Product::where('is_varied', 0)->first();
    // //    $variant = ProductVariant::where('product_id', $product->id)->first();
    // //     $promo   = Promo::where('type', 'percentage')->first();

    // //     $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
    // //         'address_id'                => 1,
    // //         'promo_id'                  => $promo->id,
    // //         'delivery_provider'         => 'USPS',
    // //         'delivery_service'          => 'Priority 1 day',
    // //         'delivery_estimate_day'     => '1-3',
    // //         'delivery_fee'              => 123.43,
    // //         'products'                  => [
    // //             [
    // //                 'id'       => $product->id,
    // //                 'variant_id'    => $variant->id,
    // //                 'quantity' => 1
    // //             ]
    // //         ],
    // //     ]);

    // //     $response->assertStatus(201);
    // // }

    // // public function test_percentage_promo_uqual_to_total_purchased_at_users_store_orders()
    // // {
    // //     $faker = \Faker\Factory::create();

    // //     $user = USer::where('is_active', 1)->first();

    // //     $this->actingAs($user, 'api_user');

    // //     $product = Product::where('is_varied', 0)->first();
    // //    $variant = ProductVariant::where('product_id', $product->id)->first();
    // //     $promo   = Promo::where('type', 'percentage')->first();

    // //     $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
    // //         'address_id'                => 1,
    // //         'promo_id'                  => $promo->id,
    // //         'delivery_provider'         => 'USPS',
    // //         'delivery_service'          => 'Priority 1 day',
    // //         'delivery_estimate_day'     => '1-3',
    // //         'delivery_fee'              => 123.43,
    // //         'products'                  => [
    // //             [
    // //                 'id'       => $product->id,
    // //                 'variant_id'    => $variant->id,
    // //                 'quantity' => 1
    // //             ]
    // //         ],
    // //     ]);

    // //     $order = Order::orderBy('id', 'DESC')->where('promo_id', $promo->id)->first();

    // //     $amountPromo                = ($order->total_purchased * $promo->amount)/100;
    // //     $totalPurchasedDiscounted   = $order->total_purchased - $amountPromo;
    // //     $reCalculateGrandTotal      = $order->delivery_fee + $totalPurchasedDiscounted;

    // //     $this->assertSame(number_format($reCalculateGrandTotal, 2), $order->grand_total_purchased);

    // //     $response->assertStatus(201);
    // // }

    public function test_users_get_detail_orders()
    {
        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $order = Order::where('user_id', $user->id)->first();

        $response = $this->get('/api/v1/users/' . $user->uuid . '/orders/' . $order->uuid);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'uuid',
                'total_purchased',
                'total_usd_purchased',
                'total_additional_purchased',
                'total_usd_additional_purchased',
                'grand_total_purchased',
                'grand_total_usd_purchased',
                'payment_status',
                'payment_url',
                'status',
                'details' => [],
                'additionals' => [],
                'forms' => [],
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_products_variant_id_required_at_users_store_orders()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 1)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'                  => [
                [
                    'id'       => $product->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.variant_id');
    }

    public function test_invalid_variant_id_store_order_with_variant_product()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', '!=', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'            => $product->id,
                    'variant_id'    => $variant->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.variant_id');
    }

    public function test_user_store_order_with_variant_product()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();


        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'      => [
                [
                    'id'         => $product->id,
                    'variant_id' => $variant->id,
                    'quantity'   => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_max_order_at_user_store_order()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 0)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $product->save();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'       => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_multiple_product_at_store_order()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $product2 = Product::where('is_varied', 0)->first();

        $variant2 = ProductVariant::where('product_id', $product2->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            
            
            'products'                  => [
                [
                    'id'         => $product->id,
                    'variant_id' => $variant->id,
                    'quantity'   => 1
                ],
                [
                    'id'         => $product2->id,
                    'variant_id' => $variant2->id,
                    'quantity'   => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ],
                [
                    'product_id'                => $product2->id,
                    'variant_id'                => $variant2->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_variant_id_required_at_unvaried_product_at_store_order()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $product2 = Product::where('is_varied', 0)->first();

        $variant2 = ProductVariant::where('product_id', $product2->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            
            
            'products'                  => [
                [
                    'id'         => $product->id,
                    'variant_id' => $variant->id,
                    'quantity'   => 1
                ],
                [
                    'id'         => $product2->id,
                    'quantity'   => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ],
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.1.variant_id');
    }

    public function test_multiple_product_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $variant2 = ProductVariant::where('product_id', $product->id)->where('id', '!=', $variant->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $productAdditional2 = ProductAdditional::where('product_id', $product->id)->where('id', '!=', $productAdditional->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'                  => [
                [
                    'id'         => $product->id,
                    'variant_id' => $variant->id,
                    'quantity'   => 2
                ],
                [
                    'id'         => $product->id,
                    'variant_id' => $variant2->id,
                    'quantity'   => 2
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 2
                ],
                [
                    'id'        => $productAdditional2->id,
                    'quantity'  => 2
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ],
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant2->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ],
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ],
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant2->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $newOrder               = Order::where('user_id', $user->id)->latest()->first();
        $totalPriceProduct      = ($variant->price * 2 + ($variant2->price * 2));
        $totalUsdPriceProduct   = ($variant->usd_price * 2 + ($variant2->usd_price * 2));
        $totalEurPriceProduct   = ($variant->eur_price * 2 + ($variant2->eur_price * 2));
        $totalJpyPriceProduct   = ($variant->jpy_price * 2 + ($variant2->jpy_price * 2));
        $totalCnyPriceProduct   = ($variant->cny_price * 2 + ($variant2->cny_price * 2));
        
        $totalPriceAdditional       = (($productAdditional->additionalData->price * 2) + ($productAdditional2->additionalData->price * 2));
        $totalUsdPriceAdditional    = (($productAdditional->additionalData->usd_price * 2) + ($productAdditional2->additionalData->usd_price * 2));
        $totalEurPriceAdditional    = (($productAdditional->additionalData->eur_price * 2) + ($productAdditional2->additionalData->eur_price * 2));
        $totalJpyPriceAdditional    = (($productAdditional->additionalData->jpy_price * 2) + ($productAdditional2->additionalData->jpy_price * 2));
        $totalCnyPriceAdditional    = (($productAdditional->additionalData->cny_price * 2) + ($productAdditional2->additionalData->cny_price * 2));

        $this->assertEquals(number_format($totalPriceProduct, 2, '.', ''), $newOrder->total_purchased);
        $this->assertEquals(number_format($totalUsdPriceProduct, 2, '.', ''), $newOrder->total_usd_purchased);
        $this->assertEquals(number_format($totalEurPriceProduct, 2, '.', ''), $newOrder->total_eur_purchased);
        $this->assertEquals(number_format($totalJpyPriceProduct, 2, '.', ''), $newOrder->total_jpy_purchased);
        $this->assertEquals(number_format($totalCnyPriceProduct, 2, '.', ''), $newOrder->total_cny_purchased);

        $this->assertEquals(number_format($totalPriceAdditional, 2, '.', ''), $newOrder->total_additional_purchased);
        $this->assertEquals(number_format($totalUsdPriceAdditional, 2, '.', ''), $newOrder->total_usd_additional_purchased);
        $this->assertEquals(number_format($totalEurPriceAdditional, 2, '.', ''), $newOrder->total_eur_additional_purchased);
        $this->assertEquals(number_format($totalJpyPriceAdditional, 2, '.', ''), $newOrder->total_jpy_additional_purchased);
        $this->assertEquals(number_format($totalCnyPriceAdditional, 2, '.', ''), $newOrder->total_cny_additional_purchased);

        $this->assertEquals(number_format(($totalPriceAdditional + $totalPriceProduct), 2, '.', ''), $newOrder->grand_total_purchased);
        $this->assertEquals(number_format(($totalUsdPriceAdditional + $totalUsdPriceProduct), 2, '.', ''), $newOrder->grand_total_usd_purchased);
        $this->assertEquals(number_format(($totalEurPriceAdditional + $totalEurPriceProduct), 2, '.', ''), $newOrder->grand_total_eur_purchased);
        $this->assertEquals(number_format(($totalJpyPriceAdditional + $totalJpyPriceProduct), 2, '.', ''), $newOrder->grand_total_jpy_purchased);
        $this->assertEquals(number_format(($totalCnyPriceAdditional + $totalCnyPriceProduct), 2, '.', ''), $newOrder->grand_total_cny_purchased);

        $response->assertStatus(201);
    }

    public function test_product_id_required_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();


        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'            => null,
                    'quantity'      => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.id');
    }

    public function test_product_quantity_required_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'            => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => null
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.quantity');
    }

    public function test_product_quantity_gte_than_0_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            'products'                  => [
                [
                    'id'            => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => 0
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.quantity');
    }

    public function test_product_id_distinc_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'products'                  => [
                [
                    'id'            => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => 1
                ],
                [
                    'id'            => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.variant_id');
    }

    public function test_product_variant_id_distinc_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            
            
            
            'products'                  => [
                [
                    'id'            => $product->id,
                   'variant_id'    => $variant->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => 1
                ],
                [
                    'id'            => $product->id,
                   'variant_id'    => $variant->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 1
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.variant_id');
    }

    public function test_additional_qty_limited_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $productAdditional = ProductAdditional::whereHas('additionalData', function($q) {
            $q->where('is_qty_limited', 1);
        })->first();

        $product = Product::where('is_varied', 1)->where('id', $productAdditional->product_id)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'arrival_time'              => '11:00',
            
            
            'products'                  => [
                [
                    'id'            => $product->id,
                   'variant_id'    => $variant->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 5
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertStatus(422);
    }

    public function test_additional_qty_limited_false_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = USer::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $productAdditional = ProductAdditional::whereHas('additionalData', function($q) {
            $q->where('is_qty_limited', 0);
        })->first();

        $product = Product::where('is_varied', 1)->where('id', $productAdditional->product_id)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/orders', [
            'activity_date'         => '2024-10-10',
            'emergency_name'        => 'John',
            'emergency_relation'    => 'father',
            'emergency_language'    => 'english',
            'emergency_phone_number'=> '+218872323',
            'arrival_time'              => '11:00',
            
            
            'products'                  => [
                [
                    'id'            => $product->id,
                   'variant_id'    => $variant->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => 1
                ]
            ],
            'additionals' => [
                [
                    'id'        => $productAdditional->id,
                    'quantity'  => 5
                ]
            ],
            'forms' => [
                [
                    'product_id'                => $product->id,
                    'variant_id'                => $variant->id,
                    'name'                      => $faker->name(),
                    'date_of_birth'             => '1994-10-10',
                    'gender'                    => 'male',
                    'nationality'               => 'usa',
                    'pickup_address'            => 'canggu',
                    'diet_restriction'          => 'vegetarian',
                    'shoes_size'                => '21',
                    'diving_association_number' => 'njn0shs8',
                    'last_dive'                 => '2022-10-10',
                    'email'                     => 'test@gmail.com',
                    'phone'                     => '+173282392',
                    'is_pic'                    => 0
                ]
            ]
        ]);

        $response->assertStatus(201);
    }
}
