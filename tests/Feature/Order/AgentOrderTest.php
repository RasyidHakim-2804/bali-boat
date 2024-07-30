<?php

namespace Tests\Feature\Order;

use App\Models\Agent;
use App\Models\AgentSpecialPrice;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductAdditional;
use App\Models\ProductVariant;
use App\Models\SpecialPriceCalender;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AgentOrderTest extends TestCase
{
    public function test_agents_get_list_orders()
    {
        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $response = $this->get('/api/v1/agents/' . $agent->uuid . '/orders');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_agents_id_parameter_agents_get_detail_orders()
    {
        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $response = $this->get('/api/v1/agents/' . $agent->uuid . '/orders/100000000');

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

    public function test_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '09:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

        $cart = $agent->cart;

        $this->assertTrue(($cart->details->count() > 0));

        $order = Order::where('agent_id', $agent->id)->orderBy('id', 'DESC')->first();

        $this->assertEquals($order->total_purchased, $product->nett_price);

        $response->assertStatus(201);
    }

    public function test_agents_store_orders_from_cart()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '09:00',
            'is_by_cart'                => 1,
            'is_pay_in_system'          => 1,
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
        $cart = $agent->cart;

        $this->assertEquals(0, $cart->details->count());

        $order = Order::where('agent_id', $agent->id)->orderBy('id', 'DESC')->first();

        $this->assertEquals($order->total_purchased, $product->nett_price);

        $response->assertStatus(201);
    }

    public function test_agents_store_order_with_null_additional()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '09:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
            'products'                  => [
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

        $order = Order::where('agent_id', $agent->id)->orderBy('id', 'DESC')->first();

        $this->assertEquals($order->total_purchased, $product->nett_price);

        $response->assertStatus(201);
    }

    public function test_agents_store_orders_without_payment()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '09:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 0,
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

        $order = Order::where('agent_id', $agent->id)->orderBy('id', 'DESC')->first();

        $this->assertEquals($order->total_purchased, $product->nett_price);

        $response->assertStatus(422);
    }

    public function test_agents_store_orders_with_not_saleable_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_saleable', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '09:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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


        $response->assertSessionHasErrors('products.0.id');
    }

    public function test_additionals_id_required_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_additionals_id_invalid_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_additionals_quantity_required_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_additionals_quantity_invalid_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_forms_required_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_forms_product_id_required_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_forms_product_id_invalid_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_forms_product_name_required_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_forms_age_required_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_forms_age_invalid_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_forms_email_required_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_forms_email_invalid_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_forms_phone_required_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_forms_is_pic_required_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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
                    'phone'      => '+612392383',
                    'is_pic'     => null
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.is_pic');
    }

    public function test_forms_is_pic_invalid_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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
                    'phone'      => '+612392383',
                    'is_pic'     => 'null'
                ]
            ]
        ]);

        $response->assertSessionHasErrors('forms.0.is_pic');
    }

    public function test_forms_is_pic_no_true_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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
                    'phone'      => '+612392383',
                    'is_pic'     => 0
                ]
            ]
        ]);

        $response->assertStatus(422);
    }

    public function test_forms_quantity_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    // public function test_fixed_promo_apply_at_agents_store_orders()
    // {
    //     $faker = \Faker\Factory::create();

    //     $agent = Agent::where('id', 2)->first();

    //     $this->actingAs($agent, 'api_agent');

    //     $product = Product::where('is_varied', 0)->first();
    //     $promo   = Promo::where('type', 'fixed')->first();

    //     $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
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

    // public function test_fixed_promo_uqual_to_total_purchased_at_agents_store_orders()
    // {
    //     $faker = \Faker\Factory::create();

    //     $agent = Agent::where('id', 2)->first();

    //     $this->actingAs($agent, 'api_agent');

    //     $product = Product::where('is_varied', 0)->first();
    //     $promo   = Promo::where('type', 'fixed')->first();

    //     $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
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

    // public function test_percentage_promo_apply_at_agents_store_orders()
    // {
    //     $faker = \Faker\Factory::create();

    //     $agent = Agent::where('id', 2)->first();

    //     $this->actingAs($agent, 'api_agent');

    //     $product = Product::where('is_varied', 0)->first();
    //     $promo   = Promo::where('type', 'percentage')->first();

    //     $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
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

    // public function test_percentage_promo_uqual_to_total_purchased_at_agents_store_orders()
    // {
    //     $faker = \Faker\Factory::create();

    //     $agent = Agent::where('id', 2)->first();

    //     $this->actingAs($agent, 'api_agent');

    //     $product = Product::where('is_varied', 0)->first();
    //     $promo   = Promo::where('type', 'percentage')->first();

    //     $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
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

    public function test_agents_get_detail_orders()
    {
        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $order = Order::where('agent_id', $agent->id)->first();

        $response = $this->get('/api/v1/agents/' . $agent->uuid . '/orders/' . $order->uuid);

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

    public function test_products_variant_id_required_at_agents_store_orders()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_invalid_variant_id_store_order_with_variant_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', '!=', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_user_store_order_with_variant_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();


        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '09:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    // public function test_max_order_at_user_store_order()
    // {
    //     $faker = \Faker\Factory::create();

    //     $agent = Agent::where('id', 2)->first();

    //     $this->actingAs($agent, 'api_agent');

    //     $product = Product::where('is_varied', 0)->first();

    //     $productAdditional = ProductAdditional::where('product_id', $product->id)->first();

    //     $product->save();

    //     $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
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

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $product2 = Product::where('is_varied', 0)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '09:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_multiple_product_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $variant2 = ProductVariant::where('product_id', $product->id)->where('id', '!=', $variant->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '09:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_product_id_required_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();


        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_product_quantity_required_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_product_quantity_gte_than_0_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_product_id_distinc_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_product_variant_id_distinc_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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

    public function test_inactive_product_calender_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '10:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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
                    'variant_id' => $variant->id,
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

    public function test_special_price_calender_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $specialPriceCalender = SpecialPriceCalender::where('product_id', $product->id)->where('variant_id', $variant->id)->where('time', '11:00')->where('date', '2024-10-10')->first();

        $agentSpecialPriceCalender = AgentSpecialPrice::where('agent_id', $agent->id)->where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '11:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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
                    'variant_id' => $variant->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $order = Order::where('agent_id', $agent->id)->orderBy('id', 'DESC')->first();

        $this->assertEquals($order->total_purchased, $agentSpecialPriceCalender->nett_price);

        $response->assertStatus(201);
    }

    public function test_agent_special_price_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $product = Product::where('is_varied', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $productAdditional = ProductAdditional::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $agentSpecialPrice = AgentSpecialPrice::where('product_id', $product->id)->where('variant_id', $variant->id)->where('agent_id', $agent->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '11:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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
                    'variant_id' => $variant->id,
                    'name'       => $faker->name(),
                    'age'        => 23,
                    'email'      => 'test@gmail.com',
                    'phone'      => '+173282392',
                    'is_pic'     => 1
                ]
            ]
        ]);

        $order = Order::where('agent_id', 2)->orderBy('id', 'DESC')->first();

        $this->assertEquals($order->total_purchased, $agentSpecialPrice->nett_price);

        $response->assertStatus(201);
    }

    public function test_additional_qty_limited_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $productAdditional = ProductAdditional::whereHas('additionalData', function($q) {
            $q->where('is_qty_limited', 1);
        })->first();

        $product = Product::where('is_varied', 1)->where('id', $productAdditional->product_id)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '11:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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
                    'quantity'  => 5
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

        $response->assertStatus(422);
    }

    public function test_additional_qty_limited_false_at_store_order_with_all_varied_product()
    {
        $faker = \Faker\Factory::create();

        $agent = Agent::where('id', 2)->first();

        $this->actingAs($agent, 'api_agent');

        $productAdditional = ProductAdditional::whereHas('additionalData', function($q) {
            $q->where('is_qty_limited', 0);
        })->first();

        $product = Product::where('is_varied', 1)->where('id', $productAdditional->product_id)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $response = $this->post('/api/v1/agents/' . $agent->uuid . '/orders', [
            'activity_date'             => '2024-10-10',
            'arrival_time'              => '11:00',
            'is_by_cart'                => 0,
            'is_pay_in_system'          => 1,
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
                    'quantity'  => 5
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
}
