<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Product;
use App\Models\ProductVariant;
use Tests\TestCase;

class LogisticTest extends TestCase
{
    public function test_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => $address->id,
            'products'      => [
                [
                    'id'         => $unvariedProduct->id,
                    'variant_id' => null,
                    'quantity'   => 1
                ],
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => $variantProduct->id,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                [
                    'service',
                    'estimate_days',
                    'rate'
                ]
            ]
        ]);
    }

    public function test_generate_international_logistic()
    {
        $address            = Address::where('user_id', 3)->where('country', 'Indonesia')->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => $address->id,
            'products'      => [
                [
                    'id'         => $unvariedProduct->id,
                    'variant_id' => null,
                    'quantity'   => 1
                ],
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => $variantProduct->id,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertStatus(200);
    }

    public function test_address_id_required_at_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => null,
            'products'      => [
                [
                    'id'         => $unvariedProduct->id,
                    'variant_id' => null,
                    'quantity'   => 1
                ],
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => $variantProduct->id,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertSessionHasErrors('address_id');
    }

    public function test_address_id_exists_at_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => 10000,
            'products'      => [
                [
                    'id'         => $unvariedProduct->id,
                    'variant_id' => null,
                    'quantity'   => 1
                ],
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => $variantProduct->id,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertSessionHasErrors('address_id');
    }

    public function test_products_required_at_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => $address->id,
            'products'      => null,
        ]);

        $response->assertSessionHasErrors('products');
    }

    public function test_products_id_required_at_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => $address->id,
            'products'      => [
                [
                    'id'         => null,
                    'variant_id' => null,
                    'quantity'   => 1
                ],
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => $variantProduct->id,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertSessionHasErrors('products.0.id');
    }

    public function test_products_id_exists_at_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => $address->id,
            'products'      => [
                [
                    'id'         => 10000,
                    'variant_id' => null,
                    'quantity'   => 1
                ],
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => $variantProduct->id,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertSessionHasErrors('products.0.id');
    }

    public function test_products_quantity_required_at_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => $address->id,
            'products'      => [
                [
                    'id'         => $unvariedProduct->id,
                    'variant_id' => null,
                    'quantity'   => null
                ],
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => $variantProduct->id,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertSessionHasErrors('products.0.quantity');
    }

    public function test_products_quantity_gretaer_than_0_at_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => $address->id,
            'products'      => [
                [
                    'id'         => $unvariedProduct->id,
                    'variant_id' => null,
                    'quantity'   => 0
                ],
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => $variantProduct->id,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertSessionHasErrors('products.0.quantity');
    }

    public function test_products_quantity_integer_at_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => $address->id,
            'products'      => [
                [
                    'id'         => $unvariedProduct->id,
                    'variant_id' => null,
                    'quantity'   => 3.5
                ],
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => $variantProduct->id,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertSessionHasErrors('products.0.quantity');
    }

    public function test_products_variant_id_required_at_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => $address->id,
            'products'      => [
                [
                    'id'         => $unvariedProduct->id,
                    'variant_id' => null,
                    'quantity'   => 1
                ],
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => null,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_products_variant_id_exists_at_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => $address->id,
            'products'      => [
                [
                    'id'         => $unvariedProduct->id,
                    'variant_id' => null,
                    'quantity'   => 1
                ],
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => 10000,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertSessionHasErrors('products.1.variant_id');
    }

    public function test_products_id_distinct_at_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => $address->id,
            'products'      => [
                [
                    'id'         => $unvariedProduct->id,
                    'variant_id' => null,
                    'quantity'   => 1
                ],
                [
                    'id'         => $unvariedProduct->id,
                    'variant_id' => null,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertSessionHasErrors('products.0.id');
    }

    public function test_products_variant_id_distinct_at_generate_logistic()
    {
        $address            = Address::where('user_id', 3)->first();
        $unvariedProduct    = Product::where('is_variant', 0)->first();
        $variedProduct      = Product::where('is_variant', 1)->first();
        $variantProduct     = ProductVariant::where('product_id', $variedProduct->id)->first();

        $response = $this->post('/api/v1/logistics', [
            'address_id'    => $address->id,
            'products'      => [
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => $variantProduct->id,
                    'quantity'   => 1
                ],
                [
                    'id'         => $variedProduct->id,
                    'variant_id' => $variantProduct->id,
                    'quantity'   => 1
                ]
            ],
        ]);

        $response->assertSessionHasErrors('products.0.variant_id');
    }
}
