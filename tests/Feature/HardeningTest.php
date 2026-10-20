<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_proof_cannot_be_reached_with_odd_paths(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('proofs/bukti.jpg', 'x');
        Order::factory()->qris()->create(['payment_proof' => 'proofs/bukti.jpg']);

        $this->get('/media/./proofs/bukti.jpg')->assertNotFound();
        $this->get('/media/proofs//bukti.jpg')->assertNotFound();
        $this->get('/media/Proofs/bukti.jpg')->assertNotFound();
    }

    public function test_cart_drops_deleted_and_inactive_products(): void
    {
        $available = Product::factory()->create();
        $hidden = Product::factory()->inactive()->create();
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->withSession(['cart' => [$available->id => 1, $hidden->id => 1, 99999 => 2]])
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSessionHas('cart', [$available->id => 1]);
    }

    public function test_proof_cannot_be_uploaded_to_cancelled_order(): void
    {
        Storage::fake('public');
        $order = Order::factory()->qris()->create(['status' => 'cancelled']);

        $this->actingAs($order->user)
            ->post(route('orders.proof', $order), [
                'proof' => \Illuminate\Http\UploadedFile::fake()->image('b.jpg'),
            ])
            ->assertForbidden();
    }
}
