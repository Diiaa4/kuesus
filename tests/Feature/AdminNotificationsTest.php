<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_batches_do_not_skip_orders_after_the_ten_item_limit(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $orderIds = [];

        for ($i = 1; $i <= 11; $i++) {
            $order = Order::create([
                'user_id' => User::factory()->create(['role' => 'customer'])->id,
                'code' => sprintf('SUS-TEST-%04d', $i),
                'customer_name' => 'Pelanggan '.$i,
                'phone' => '081234567890',
                'address' => 'Jl. Contoh',
                'payment_method' => 'qris',
                'payment_status' => 'unpaid',
                'status' => 'new',
                'total' => 3500,
            ]);
            $orderIds[] = $order->id;
        }

        $firstBatch = $this->actingAs($admin)
            ->getJson(route('admin.notifications', ['after' => 0]));

        $firstBatch->assertOk()
            ->assertJsonCount(10, 'orders')
            ->assertJsonPath('latest', $orderIds[9]);

        $this->getJson(route('admin.notifications', ['after' => $orderIds[9]]))
            ->assertOk()
            ->assertJsonCount(1, 'orders')
            ->assertJsonPath('orders.0.id', $orderIds[10])
            ->assertJsonPath('latest', $orderIds[10]);
    }
}
