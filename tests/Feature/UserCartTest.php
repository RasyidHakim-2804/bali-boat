<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UserCartTest extends TestCase
{
    public function test_user_get_list_cart()
    {
        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->get('/api/v1/users/' . $user->uuid . '/carts');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data',
        ]);
    }
    
    public function test_can_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => 1,
        ]);

        $response->assertStatus(201);
    }

    public function test_user_store_unvaried_product_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::orderBy('id', 'DESC')->where('is_variant', 0)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => 1,
        ]);

        $response->assertStatus(201);
    }

    public function test_user_store_cart_with_variant_product()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_variant', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->where('stock', '>', 0)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'variant_id'        => $variant->id,
            'quantity'          => 1,
        ]);

        $response->assertStatus(201);
    }

    public function test_user_store_cart_with_same_variant_product()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_variant', 1)->first();
        $variant = ProductVariant::where('product_id', $product->id)->first();

        $oldCart = Cart::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $oldCartQty = ($oldCart) ? $oldCart->quantity : 0;

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'variant_id'        => $variant->id,
            'quantity'          => 1,
        ]);

        $newCart = Cart::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $newCartQty = ($newCart) ? $newCart->quantity : 0;

        $this->assertSame((intval($oldCartQty) + 1), intval($newCartQty));

        $response->assertStatus(201);
    }

    public function test_user_store_cart_with_other_variant_product()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_variant', 1)->first();
        $variant = ProductVariant::where('product_id', $product->id)->orderBy('id', 'DESC')->first();

        $oldCartCount = Cart::where('product_id', $product->id)->count();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'variant_id'        => $variant->id,
            'quantity'          => 1,
        ]);

        $newCartCount = Cart::where('product_id', $product->id)->count();

        $this->assertSame($newCartCount, ($oldCartCount + 1));

        $response->assertStatus(201);
    }

    public function test_users_id_parameter_user_update_cart()
    {
        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $response = $this->put('/api/v1/users/' . $user->uuid . '/carts/100000000');

        $response->assertStatus(404);
    }

    public function test_can_user_update_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $cart = Cart::first();

        $product = Product::first();

        $response = $this->put('/api/v1/users/' . $user->uuid . '/carts/' . $cart->id , [
            'quantity'          => 1,
        ]);

        $newCart = Cart::where('id', $cart->id)->first();

        $this->assertSame(intval($newCart->quantity), 1);

        $response->assertStatus(201);
    }

    public function test_user_update_cart_with_zero_quantity()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $cart = Cart::first();

        $product = Product::first();

        $response = $this->put('/api/v1/users/' . $user->uuid . '/carts/' . $cart->id , [
            'quantity'          => 0,
        ]);

        $newCart = Cart::where('id', $cart->id)->count();

        $this->assertSame($newCart, 0);

        $response->assertStatus(201);
    }

    public function test_required_product_id_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => null,
            'quantity'          => 4,
        ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_exists_product_id_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => 10,
            'quantity'          => 4,
        ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_required_quantity_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => null,
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_required_variant_id_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_variant', 1)->first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => 1,
        ]);

        $response->assertStatus(422);
    }

    public function test_quantity_equal_than_stock_product_already_in_cart_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::whereHas('carts')->where('is_variant', 0)->first();

        $product->max_order = $product->stock + 3;

        $product->save();

        $cart = Cart::where('product_id', $product->id)->first();

        $acceptedQuantity = $product->stock - $cart->quantity;

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => $acceptedQuantity,
        ]);

        $response->assertStatus(201);
    }

    public function test_quantity_more_than_stock_product_already_in_cart_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::whereHas('carts')->first();

        $product->stock = $product->max_order - 1;

        $product->save();

        $cart =Cart::where('product_id', $product->id)->first();

        $acceptedQuantity = $product->stock - $cart->quantity;

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => ($cart->quantity + $acceptedQuantity + 3),
        ]);

        $response->assertStatus(422);
    }

    public function test_quantity_equal_than_stock_variant_product_already_in_cart_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::whereHas('carts')->where('is_variant', 1)->first();

        $variant = ProductVariant::whereHas('carts')->where('product_id', $product->id)->first();

        $variant->stock = $product->max_order - 1;

        $variant->save();

        $cart =Cart::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $acceptedQuantity = $variant->stock - $cart->quantity;

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'variant_id'        => $variant->id,
            'quantity'          => ($cart->quantity + $acceptedQuantity),
        ]);

        $response->assertStatus(422);
    }

    public function test_quantity_more_than_stock_variant_product_already_in_cart_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::whereHas('carts')->where('is_variant', 1)->first();

        $variant = ProductVariant::whereHas('carts')->where('product_id', $product->id)->first();

        $variant->stock = $product->max_order - 1;

        $variant->save();

        $cart =Cart::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $acceptedQuantity = $variant->stock - $cart->quantity;

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'variant_id'        => $variant->id,
            'quantity'          => ($cart->quantity + $acceptedQuantity + 3),
        ]);

        $response->assertStatus(422);
    }

    public function test_quantity_equal_than_max_order_product_already_in_cart_id_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::whereHas('carts')->where('is_variant', 0)->first();

        $product->stock = $product->max_order + 3;

        $product->save();

        $cart = Cart::where('product_id', $product->id)->where('variant_id', null)->first();

        $acceptedQuantity = $product->max_order - $cart->quantity;

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => $acceptedQuantity,
        ]);

        $response->assertStatus(201);
    }

    public function test_quantity_more_than_max_order_product_already_in_cart_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::whereHas('carts')->first();

        $cart =Cart::where('product_id', $product->id)->first();

        $acceptedQuantity = $product->max_order - $cart->quantity;

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => ($cart->quantity + $acceptedQuantity + 3),
        ]);

        $response->assertStatus(422);
    }

    public function test_quantity_equal_than_max_order_variant_product_already_in_cart_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::whereHas('carts')->where('is_variant', 1)->first();

        $variant = ProductVariant::whereHas('carts')->where('product_id', $product->id)->first();

        $cart =Cart::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $acceptedQuantity = $variant->max_order - $cart->quantity;

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'variant_id'        => $variant->id,
            'quantity'          => ($cart->quantity + $acceptedQuantity),
        ]);

        $response->assertStatus(422);
    }

    public function test_quantity_more_than_max_order_variant_product_already_in_cart_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::whereHas('carts')->where('is_variant', 1)->first();

        $variant = ProductVariant::whereHas('carts')->where('product_id', $product->id)->first();

        $cart =Cart::where('product_id', $product->id)->where('variant_id', $variant->id)->first();

        $acceptedQuantity = $variant->max_order - $cart->quantity;

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'variant_id'        => $variant->id,
            'quantity'          => ($cart->quantity + $acceptedQuantity + 3),
        ]);

        $response->assertStatus(422);
    }

    public function test_quantity_equal_with_product_stock_id_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::first();

        $product->max_order = $product->stock + 3;

        $product->save();

       Cart::where('product_id', $product->id)->delete();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => $product->stock,
        ]);

        $response->assertStatus(201);
    }

    public function test_quantity_more_than_stock_product_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::first();

        $product->stock = $product->max_order - 1;

        $product->save();

       Cart::where('product_id', $product->id)->delete();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => ($product->stock + 3),
        ]);

        $response->assertStatus(422);
    }

    public function test_quantity_equal_than_stock_variant_product_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_variant', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $variant->stock = $variant->max_order - 1;

        $variant->save();

        Cart::where('product_id', $product->id)->delete();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'variant_id'        => $variant->id,
            'quantity'          => $variant->stock,
        ]);

        $response->assertStatus(201);
    }

    public function test_quantity_more_than_stock_variant_product_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_variant', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $variant->stock = $product->max_order - 1;

        $variant->save();

       Cart::where('product_id', $product->id)->delete();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'variant_id'        => $variant->id,
            'quantity'          => ($product->stock + 3),
        ]);

        $response->assertStatus(422);
    }

    public function test_quantity_equal_than_max_order_product_id_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::first();

        $product->stock = $product->max_order + 1;

        $product->save();

       Cart::where('product_id', $product->id)->delete();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => $product->max_order,
        ]);

        $response->assertStatus(201);
    }

    public function test_quantity_more_than_max_order_product_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::first();

       Cart::where('product_id', $product->id)->delete();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => ($product->max_order + 3),
        ]);

        $response->assertStatus(422);
    }

    public function test_quantity_equal_than_max_order_variant_product_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_variant', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

        $variant->stock = $variant->max_order + 3;

        $variant->save();

        Cart::where('product_id', $product->id)->delete();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'variant_id'        => $variant->id,
            'quantity'          => $variant->max_order,
        ]);

        $response->assertStatus(201);
    }

    public function test_quantity_more_than_max_order_variant_product_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::where('is_variant', 1)->first();

        $variant = ProductVariant::where('product_id', $product->id)->first();

       Cart::where('product_id', $product->id)->delete();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'variant_id'        => $variant->id,
            'quantity'          => ($product->max_order + 3),
        ]);

        $response->assertStatus(422);
    }

    public function test_user_store_another_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => 1,
        ]);

        $response->assertStatus(201);
    }
    
    public function test_greater_than_quantity_at_user_store_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $product = Product::first();

        $response = $this->post('/api/v1/users/' . $user->uuid . '/carts', [
            'product_id'        => $product->id,
            'quantity'          => 0,
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_greater_than_quantity_at_user_update_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $cart = Cart::where('user_id', $user->id)->first();

        $response = $this->put('/api/v1/users/' . $user->uuid . '/carts/' . $cart->id, [
            'quantity' => -1,
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_user_delete_cart()
    {
        $faker = \Faker\Factory::create();

        $user = User::where('is_active', 1)->first();

        $this->actingAs($user, 'api_user');

        $cart = Cart::where('user_id', $user->id)->first();

        $response = $this->delete('/api/v1/users/' . $user->uuid . '/carts/' . $cart->id);

        $response->assertStatus(201);
    }
}
