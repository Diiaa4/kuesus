<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function getRoutes(): array
    {
        return [
            'dashboard' => ['get', '/admin'],
            'notifikasi' => ['get', '/admin/notifikasi'],
            'produk' => ['get', '/admin/produk'],
            'tambah produk' => ['get', '/admin/produk/create'],
            'pesanan' => ['get', '/admin/pesanan'],
            'ulasan' => ['get', '/admin/ulasan'],
            'inventori' => ['get', '/admin/inventori'],
            'laporan' => ['get', '/admin/laporan'],
            'ekspor laporan' => ['get', '/admin/laporan/ekspor'],
            'pengaturan' => ['get', '/admin/pengaturan'],
            'profil' => ['get', '/admin/profil'],
            'chat' => ['get', '/admin/chat'],
            'chat threads' => ['get', '/admin/chat/threads'],
        ];
    }

    #[DataProvider('getRoutes')]
    public function test_guest_is_redirected_to_login(string $method, string $uri): void
    {
        $this->$method($uri)->assertRedirect(route('login'));
    }

    #[DataProvider('getRoutes')]
    public function test_customer_is_forbidden(string $method, string $uri): void
    {
        $this->actingAs(User::factory()->create())->$method($uri)->assertForbidden();
    }

    #[DataProvider('getRoutes')]
    public function test_admin_can_open_every_page(string $method, string $uri): void
    {
        $this->seedSampleData();

        $this->actingAs(User::factory()->admin()->create())->$method($uri)->assertOk();
    }

    public function test_admin_can_open_detail_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $order = $this->seedSampleData();

        $this->actingAs($admin)->get(route('admin.orders.show', $order))->assertOk()->assertSee($order->code);
        $this->actingAs($admin)->get(route('admin.products.edit', Product::first()))->assertOk();
        $this->actingAs($admin)->get(route('admin.chat', $order->user_id))->assertOk();
    }

    private function seedSampleData(): Order
    {
        $order = Order::factory()->has(OrderItem::factory()->count(2)->state(['product_id' => null]), 'items')->create();
        Product::factory()->count(2)->create();
        Product::factory()->lowStock()->create();
        Review::factory()->create();

        return $order;
    }
}
