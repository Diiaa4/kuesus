<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_refuses_cart_with_unavailable_product(): void
    {
        $available = Product::factory()->create();
        $hidden = Product::factory()->inactive()->create();
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->withSession(['cart' => [$available->id => 1, $hidden->id => 1]])
            ->post(route('checkout.store'), [
                'customer_name' => 'Ani', 'phone' => '0812', 'address' => 'Jl. Mawar', 'payment_method' => 'cod',
            ])
            ->assertSessionHasErrors('cart');

        $this->assertSame(0, Order::count());
        $this->assertSame(30, $available->fresh()->stock);
    }

    public function test_checkout_creates_order_and_reduces_stock(): void
    {
        $product = Product::factory()->create(['price' => 4000, 'stock' => 10]);
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->withSession(['cart' => [$product->id => 3]])
            ->post(route('checkout.store'), [
                'customer_name' => 'Ani', 'phone' => '0812', 'address' => 'Jl. Mawar', 'payment_method' => 'qris',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(12000, Order::first()->total);
        $this->assertSame(7, $product->fresh()->stock);
    }

    public function test_checkout_refuses_cart_with_deleted_product(): void
    {
        $product = Product::factory()->create(['stock' => 5]);
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->withSession(['cart' => [$product->id => 1, 999999 => 1]])
            ->post(route('checkout.store'), [
                'customer_name' => 'Ani', 'phone' => '0812', 'address' => 'Jl. Mawar', 'payment_method' => 'qris',
            ])
            ->assertSessionHasErrors('cart');

        $this->assertSame(0, Order::count());
        $this->assertSame(5, $product->fresh()->stock);
    }
}
