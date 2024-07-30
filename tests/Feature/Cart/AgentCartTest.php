<?php

namespace Tests\Feature\Order;

use App\Models\Agent;
use App\Models\Cart;
use App\Models\CartAdditional;
use App\Models\CartDetail;
use App\Models\InactiveProductCalender;
use App\Models\Product;
use App\Models\ProductAdditional;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AgentCartTest extends TestCase
{
    public function test_users_get_list_carts()
    {
        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/retrive-carts');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data',
        ]);
    }

    public function test_users_id_parameter_users_get_detail_carts()
    {
        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $response = $this->get('/api/v1/agents/' . $user->uuid . '/orders/100000000');

        $response->assertStatus(404);
    }

    public function test_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'  => [
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_users_store_carts_with_null_additional()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'  => [
                [
                    'id'       => $product->id,
                    'quantity' => 1
                ]
            ],
            'additionals' => [],
            'forms' => [
                [
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_additionals_id_required_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'                  => [
                [
                    'id'       => $product->id,
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('additionals.0.id');
    }

    public function test_additionals_id_invalid_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'                  => [
                [
                    'id'       => $product->id,
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('additionals.0.id');
    }

    public function test_additionals_quantity_required_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'                  => [
                [
                    'id'       => $product->id,
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('additionals.0.quantity');
    }

    public function test_additionals_quantity_invalid_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'                  => [
                [
                    'id'       => $product->id,
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('additionals.0.quantity');
    }

    public function test_forms_required_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
        ]);

        $response->assertSessionHasErrors('forms');
    }

    public function test_forms_product_id_required_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => null,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.product_id');
    }

    public function test_forms_product_id_invalid_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => 1000,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.product_id');
    }

    public function test_forms_product_name_required_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => $product->id,
                    'name'       => null,
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.name');
    }

    public function test_forms_age_required_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => null,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.age');
    }

    public function test_forms_age_invalid_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 'null',
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.age');
    }

    public function test_forms_email_required_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => null,
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.email');
    }

    public function test_forms_email_invalid_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'null',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.email');
    }

    public function test_forms_phone_required_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => null,
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.phone');
    }

    public function test_forms_quantity_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'                  => [
                [
                    'id'       => $product->id,
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+612392383',
                    'is_pic'     => 0
                ]
            ]
        ]);

        $response->assertStatus(422);
    }

    // public function test_fixed_promo_apply_at_users_store_carts()
    // {
    //     $faker = \Faker\Factory::create();

    //     $user = Agent::where('id', 2)->first();

    //     $this->actingAs($user, 'api_agent');

    //     $product = Product::where('is_varied', 0)->first();
    //     $promo   = Promo::where('type', 'fixed')->first();

    //     $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
    //         'address_id'                => 1,
    //         'promo_id'                  => $promo->id,
    //         'delivery_provider'         => 'USPS',
    //         'delivery_service'          => 'Priority 1 day',
    //         'delivery_estimate_day'     => '1-3',
    //         'delivery_fee'              => 123.43,
    //         'products'                  => [
    //             [
    //                 'id'       => $product->id,
    //                 'quantity' => 1
    //             ]
    //         ],
    //     ]);

    //     $response->assertStatus(201);
    // }

    // public function test_fixed_promo_uqual_to_total_purchased_at_users_store_carts()
    // {
    //     $faker = \Faker\Factory::create();

    //     $user = Agent::where('id', 2)->first();

    //     $this->actingAs($user, 'api_agent');

    //     $product = Product::where('is_varied', 0)->first();
    //     $promo   = Promo::where('type', 'fixed')->first();

    //     $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
    //         'address_id'                => 1,
    //         'promo_id'                  => $promo->id,
    //         'delivery_provider'         => 'USPS',
    //         'delivery_service'          => 'Priority 1 day',
    //         'delivery_estimate_day'     => '1-3',
    //         'delivery_fee'              => 123.43,
    //         'products'                  => [
    //             [
    //                 'id'       => $product->id,
    //                 'quantity' => 1
    //             ]
    //         ],
    //     ]);

    //     $order = Order::orderBy('id', 'DESC')->where('promo_id', $promo->id)->first();

    //     $totalPurchasedDiscounted   = $order->total_purchased - $promo->amount;
    //     $reCalculateGrandTotal      = $order->delivery_fee + $totalPurchasedDiscounted;

    //     $this->assertSame(number_format($reCalculateGrandTotal, 2), $order->grand_total_purchased);

    //     $response->assertStatus(201);
    // }

    // public function test_percentage_promo_apply_at_users_store_carts()
    // {
    //     $faker = \Faker\Factory::create();

    //     $user = Agent::where('id', 2)->first();

    //     $this->actingAs($user, 'api_agent');

    //     $product = Product::where('is_varied', 0)->first();
    //     $promo   = Promo::where('type', 'percentage')->first();

    //     $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
    //         'address_id'                => 1,
    //         'promo_id'                  => $promo->id,
    //         'delivery_provider'         => 'USPS',
    //         'delivery_service'          => 'Priority 1 day',
    //         'delivery_estimate_day'     => '1-3',
    //         'delivery_fee'              => 123.43,
    //         'products'                  => [
    //             [
    //                 'id'       => $product->id,
    //                 'quantity' => 1
    //             ]
    //         ],
    //     ]);

    //     $response->assertStatus(201);
    // }

    // public function test_percentage_promo_uqual_to_total_purchased_at_users_store_carts()
    // {
    //     $faker = \Faker\Factory::create();

    //     $user = Agent::where('id', 2)->first();

    //     $this->actingAs($user, 'api_agent');

    //     $product = Product::where('is_varied', 0)->first();
    //     $promo   = Promo::where('type', 'percentage')->first();

    //     $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
    //         'address_id'                => 1,
    //         'promo_id'                  => $promo->id,
    //         'delivery_provider'         => 'USPS',
    //         'delivery_service'          => 'Priority 1 day',
    //         'delivery_estimate_day'     => '1-3',
    //         'delivery_fee'              => 123.43,
    //         'products'                  => [
    //             [
    //                 'id'       => $product->id,
    //                 'quantity' => 1
    //             ]
    //         ],
    //     ]);

    //     $order = Order::orderBy('id', 'DESC')->where('promo_id', $promo->id)->first();

    //     $amountPromo                = ($order->total_purchased * $promo->amount)/100;
    //     $totalPurchasedDiscounted   = $order->total_purchased - $amountPromo;
    //     $reCalculateGrandTotal      = $order->delivery_fee + $totalPurchasedDiscounted;

    //     $this->assertSame(number_format($reCalculateGrandTotal, 2), $order->grand_total_purchased);

    //     $response->assertStatus(201);
    // }

    public function test_products_variant_id_required_at_users_store_carts()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertStatus(422);
    }

    public function test_invalid_variant_id_store_cart_with_variant_product()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', '!=', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'                  => [
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.variant_id');
    }

    public function test_user_store_cart_with_variant_product()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();


        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'                  => [
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
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    // public function test_max_cart_at_user_store_cart()
    // {
    //     $faker = \Faker\Factory::create();

    //     $user = Agent::where('id', 2)->first();

    //     $this->actingAs($user, 'api_agent');

    //     $product = Product::where('is_varied', 0)->first();

    //     $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

    //     $product->save();

    //     $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
    //         'activity_date'             => '2024-10-10',
    //         'arrival_time'              => '10:00',
    //         'products'                  => [
    //             [
    //                 'id'       => $product->id,
    //                 'quantity' => 1
    //             ]
    //         ],
    //         'additionals' => [
    //             [
    //                 'id'        => $productAdditional->id,
    //                 'quantity'  => 1
    //             ]
    //         ],
    //         'forms' => [
    //             [
    //                 'product_id' => $product->id,
    //                 'name'       => $faker->name(),
    //                 'age'        => 23,
    //                 'email'      => 'test@gmail.com',
    //                 'phone'      => '+173282392',
    //                 'is_pic'     => 1
    //             ]
    //         ]
    //     ]);

    //     $response->assertStatus(201);
    // }

    public function test_multiple_product_at_store_order()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $product2 = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ],
                [
                    'product_id' => $product2->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'is_pic'     => 0
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_multiple_product_at_store_cart_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $variant2 = ProductVariant::where('product_id', $product->id)->where('id', '!=', $variant->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'                  => [
                [
                    'id'         => $product->id,
                    'variant_id' => $variant->id,
                    'quantity'   => 1
                ],
                [
                    'id'         => $product->id,
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
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ],
                [
                    'product_id' => $product->id,
                    'variant_id' => $variant2->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'is_pic'     => 0
                ]
            ]
        ]);

        

        $response->assertStatus(201);
    }

    public function test_product_id_required_at_store_cart_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();


        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.id');
    }

    public function test_product_quantity_required_at_store_cart_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.quantity');
    }

    public function test_product_quantity_gte_than_0_at_store_cart_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.quantity');
    }

    public function test_product_id_distinc_at_store_cart_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'                  => [
                [
                    'id'            => $product->id,
                    'variant_id'    => $variant->id,
                    'quantity'      => 1
                ],
                [
                    'id'            => $product->id,
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertStatus(422);
    }

    public function test_product_variant_id_distinc_at_store_cart_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
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
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertSessionHasErrors('products.0.variant_id');
    }

    public function test_product_id_required_delete_cart()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $cart = Cart::orderBy('id', 'DESC')->first();

        $additional = CartAdditional::where('cart_id', $cart->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/delete_cart', [
            'product_id' => null
        ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_product_id_invalid_delete_cart()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $cart = Cart::orderBy('id', 'DESC')->first();

        $additional = CartAdditional::where('cart_id', $cart->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/delete_cart', [
            'product_id' => 10000
        ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_delete_cart()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $cart = Cart::orderBy('id', 'DESC')->first();

        $additional = CartAdditional::where('cart_id', $cart->id)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/delete_cart', [
            'product_id' => $additional->product_id
        ]);

        $cartDetailCount     = CartDetail::where('cart_id', $cart->id)->where('product_id', $additional->product_id)->count();
        $cartAdditionalCount = CartDetail::where('cart_id', $cart->id)->where('product_id', $additional->product_id)->count();

        $this->assertSame(0, $cartDetailCount);
        $this->assertSame(0, $cartAdditionalCount);

        $response->assertStatus(201);
    }

    public function test_users_store_carts_in_inactive_calender()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $inactiveCalender = InactiveProductCalender::first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'  => [
                [
                    'id'            => $inactiveCalender->product_id,
                    'variant_id'    => $inactiveCalender->variant_id,
                    'quantity'      => 1
                ]
            ],
            'forms' => [
                [
                    'product_id' => $inactiveCalender->product_id,
                    'variant_id' => $inactiveCalender->variant_id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_retrive_cart_with_inactive_product()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $inactiveCalender = InactiveProductCalender::first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/retrive-carts', [
            'activity_date' => date('Y-m-d', strtotime($inactiveCalender->date)),
            'arrival_time'  => date('H:i', strtotime($inactiveCalender->time))
        ]);

        $cartDetails = CartDetail::whereHas('cart', function($query) use($user) {
            $query->where('agent_id', $user->id);
        })->get();

        $isAvailable = 1;

        foreach ($cartDetails as $key => $detail) {
            if (($detail->product_id == $inactiveCalender->product_id) && ($detail->variant_id == $inactiveCalender->variant_id)) {
                $isAvailable = $detail->is_available;
            }
        }

        $this->assertSame(0, $isAvailable);
    }

    public function test_retrive_cart_with_unsaleable_product()
    {
        $faker = \Faker\Factory::create();

        $user = Agent::where('id', 2)->first();

        $this->actingAs($user, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $response = $this->post('/api/v1/agents/' . $user->uuid . '/carts', [
            
            'products'  => [
                [
                    'id'            => $product->id,
                    'quantity'      => 1
                ]
            ],
            'forms' => [
                [
                    'product_id' => $product->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $updatedProduct = Product::where('id', $product->id)->update(['is_saleable' => 0]);

        $response2 = $this->post('/api/v1/agents/' . $user->uuid . '/retrive-carts');

        $isAvailable = 1;

        foreach ($response2['data']['details'] as $key => $detail) {
            if (($detail['product_id'] == $product->id)) {
                $isAvailable = $detail['is_available'];
            }
        }

        Product::where('id', $product->id)->update(['is_saleable' => 1]);

        $this->assertSame(0, $isAvailable);

        // $response->assertStatus(201);
    }
}
