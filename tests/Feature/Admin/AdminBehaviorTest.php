<?php

namespace Tests\Feature\Admin;

use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBehaviorTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_settings_save_text_fields(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.settings.update'), ['name' => 'Toko Baru', 'phone' => '0811'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame('Toko Baru', Setting::site()['name']);
    }

    public function test_settings_upload_qris_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->put(route('admin.settings.update'), [
                'name' => 'Toko',
                'qris' => UploadedFile::fake()->image('qris.png'),
            ])
            ->assertSessionHasNoErrors();

        $path = Setting::site()['qris_image'];
        $this->assertNotSame('', $path);
        Storage::disk('public')->assertExists($path);
        $this->assertDatabaseMissing('settings', ['key' => 'qris']);
    }

    public function test_settings_can_clear_optional_field(): void
    {
        Setting::put(['name' => 'Toko', 'phone' => '0811']);

        $this->actingAs($this->admin)
            ->put(route('admin.settings.update'), ['name' => 'Toko', 'phone' => ''])
            ->assertSessionHasNoErrors();

        $this->assertSame('', Setting::site()['phone']);
    }

    public function test_notifications_report_new_orders_and_counts(): void
    {
        $old = Order::factory()->create();
        $new = Order::factory()->create();
        Product::factory()->lowStock()->create();
        Message::factory()->create();

        $this->actingAs($this->admin)
            ->getJson(route('admin.notifications', ['after' => $old->id]))
            ->assertOk()
            ->assertJsonCount(1, 'orders')
            ->assertJsonPath('orders.0.code', $new->code)
            ->assertJsonPath('counts.orders', 2)
            ->assertJsonPath('counts.unread', 1)
            ->assertJsonPath('counts.low', 1);
    }

    public function test_report_accepts_only_end_date(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.reports.index', ['to' => now()->toDateString()]))
            ->assertOk()
            ->assertSessionHasNoErrors();
    }

    public function test_report_rejects_end_before_start(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.reports.index', ['from' => '2026-09-10', 'to' => '2026-09-01']))
            ->assertSessionHasErrors('to');
    }

    public function test_report_excludes_cancelled_orders_from_totals(): void
    {
        Order::factory()->create(['total' => 20000]);
        Order::factory()->status('cancelled')->create(['total' => 99000]);

        $this->actingAs($this->admin)
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertViewHas('revenue', 20000)
            ->assertViewHas('orderCount', 1);
    }

    public function test_report_export_downloads_csv_without_cancelled_orders(): void
    {
        $kept = Order::factory()->create();
        $dropped = Order::factory()->status('cancelled')->create();

        $csv = $this->actingAs($this->admin)
            ->get(route('admin.reports.export'))
            ->assertOk()
            ->assertDownload()
            ->streamedContent();

        $this->assertStringContainsString($kept->code, $csv);
        $this->assertStringNotContainsString($dropped->code, $csv);
    }

    public function test_report_export_neutralises_spreadsheet_formulas(): void
    {
        Order::factory()->create(['customer_name' => '=HYPERLINK("http://evil.test")']);

        $csv = $this->actingAs($this->admin)->get(route('admin.reports.export'))->streamedContent();

        $this->assertStringNotContainsString(',=HYPERLINK', $csv);
        $this->assertStringNotContainsString('"=HYPERLINK', $csv);
    }

    public function test_dashboard_sales_ignore_cancelled_orders(): void
    {
        Order::factory()->create(['total' => 30000]);
        Order::factory()->status('cancelled')->create(['total' => 70000]);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('salesTotal', 30000)
            ->assertViewHas('todayCount', 1);
    }

    public function test_dashboard_range_switches_to_30_days(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['range' => 30]))
            ->assertOk()
            ->assertViewHas('range', 30);
    }

    public function test_dashboard_low_stock_ignores_hidden_products(): void
    {
        Product::factory()->lowStock()->inactive()->create();
        Product::factory()->lowStock()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertViewHas('lowCount', 1);
    }

    public function test_cancelling_order_returns_stock_once(): void
    {
        $product = Product::factory()->create(['stock' => 5]);
        $order = Order::factory()->create();
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id, 'qty' => 3]);

        $payload = ['status' => 'cancelled', 'payment_status' => 'unpaid'];
        $this->actingAs($this->admin)->patch(route('admin.orders.update', $order), $payload);
        $this->actingAs($this->admin)->patch(route('admin.orders.update', $order), $payload);

        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_cancelled_order_cannot_be_reopened(): void
    {
        $order = Order::factory()->status('cancelled')->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update', $order), ['status' => 'processing', 'payment_status' => 'paid'])
            ->assertSessionHas('error');

        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_cod_order_becomes_paid_when_delivered(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update', $order), ['status' => 'delivered', 'payment_status' => 'unpaid']);

        $this->assertSame('paid', $order->fresh()->payment_status);
    }

    public function test_qris_order_cannot_be_delivered_while_unpaid(): void
    {
        $order = Order::factory()->qris()->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update', $order), ['status' => 'delivered', 'payment_status' => 'unpaid'])
            ->assertSessionHas('error');

        $this->assertSame('new', $order->fresh()->status);
    }

    public function test_order_list_filters_by_search_status_and_payment(): void
    {
        $match = Order::factory()->qris()->create(['customer_name' => 'Budi Santoso', 'payment_status' => 'waiting_verification']);
        Order::factory()->create(['customer_name' => 'Budi Lain']);
        Order::factory()->status('delivered')->create(['customer_name' => 'Budi Santoso']);
        Order::factory()->create(['customer_name' => 'Budi Santoso', 'payment_status' => 'unpaid']);

        $this->actingAs($this->admin)
            ->get(route('admin.orders.index', ['q' => 'Santoso', 'status' => 'new', 'payment' => 'waiting_verification']))
            ->assertOk()
            ->assertViewHas('orders', fn ($orders) => $orders->pluck('id')->all() === [$match->id]);
    }

    public function test_search_treats_percent_sign_literally(): void
    {
        Order::factory()->create(['customer_name' => 'Ani']);

        $this->actingAs($this->admin)
            ->get(route('admin.orders.index', ['q' => '%']))
            ->assertViewHas('orders', fn ($orders) => $orders->isEmpty());
    }

    public function test_inventory_update_and_low_filter(): void
    {
        $low = Product::factory()->lowStock(2)->create();
        Product::factory()->create(['stock' => 50]);

        $this->actingAs($this->admin)
            ->get(route('admin.inventory.index', ['low' => 1]))
            ->assertViewHas('products', fn ($p) => $p->pluck('id')->all() === [$low->id]);

        $this->actingAs($this->admin)
            ->patch(route('admin.inventory.update', $low), ['stock' => 40])
            ->assertSessionHasNoErrors();
        $this->assertSame(40, $low->fresh()->stock);
    }

    public function test_inventory_rejects_negative_stock(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.inventory.update', $product), ['stock' => -1])
            ->assertSessionHasErrors('stock');
    }

    public function test_product_create_update_delete_with_image(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $this->post(route('admin.products.store'), [
            'name' => 'Sus Keju', 'price' => 4500, 'stock' => 10, 'is_active' => 1,
            'image' => UploadedFile::fake()->image('a.jpg'),
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::firstWhere('name', 'Sus Keju');
        Storage::disk('public')->assertExists($product->image);
        $oldImage = $product->image;

        $this->put(route('admin.products.update', $product), [
            'name' => 'Sus Keju Baru', 'price' => 5000, 'stock' => 9,
            'image' => UploadedFile::fake()->image('b.jpg'),
        ]);
        $product->refresh();
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($product->image);
        $this->assertFalse($product->is_active);

        $this->delete(route('admin.products.destroy', $product));
        $this->assertModelMissing($product);
    }

    public function test_deleting_product_keeps_order_history(): void
    {
        $product = Product::factory()->create();
        $item = OrderItem::factory()->create(['product_id' => $product->id, 'name' => 'Sus Lama']);

        $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product));

        $this->assertNull($item->fresh()->product_id);
        $this->assertSame('Sus Lama', $item->fresh()->name);
    }

    public function test_product_validation_rejects_bad_input(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.products.store'), ['name' => '', 'price' => -5, 'stock' => 'x'])
            ->assertSessionHasErrors(['name', 'price', 'stock']);
    }

    public function test_admin_replies_in_chat_and_marks_customer_messages_read(): void
    {
        $customer = User::factory()->create();
        $incoming = Message::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($this->admin)
            ->getJson(route('admin.chat.messages', $customer))
            ->assertOk()
            ->assertJsonCount(1);
        $this->assertNotNull($incoming->fresh()->read_at);

        $this->actingAs($this->admin)
            ->postJson(route('admin.chat.send', $customer), ['body' => 'Siap kak'])
            ->assertOk()
            ->assertJsonPath('sender_id', $this->admin->id);
    }

    public function test_admin_cannot_open_chat_with_another_admin(): void
    {
        $other = User::factory()->admin()->create();

        $this->actingAs($this->admin)->get(route('admin.chat', $other))->assertNotFound();
    }

    public function test_deleting_review_removes_it(): void
    {
        $review = Review::factory()->create();

        $this->actingAs($this->admin)->delete(route('admin.reviews.destroy', $review));

        $this->assertModelMissing($review);
    }

    public function test_admin_profile_update_changes_password_only_with_current_password(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.profile.update'), [
                'name' => 'Admin', 'email' => $this->admin->email,
                'current_password' => 'salah', 'password' => 'baru1234', 'password_confirmation' => 'baru1234',
            ])
            ->assertSessionHasErrors('current_password');

        $this->actingAs($this->admin)
            ->put(route('admin.profile.update'), [
                'name' => 'Admin', 'email' => $this->admin->email,
                'current_password' => 'password', 'password' => 'baru1234', 'password_confirmation' => 'baru1234',
            ])
            ->assertSessionHasNoErrors();
    }

    public function test_order_page_links_to_whatsapp_with_country_code(): void
    {
        $order = Order::factory()->create(['phone' => '0812-3456-7890']);

        $this->actingAs($this->admin)
            ->get(route('admin.orders.show', $order))
            ->assertSee('https://wa.me/6281234567890', false);
    }

    public function test_payment_proof_is_visible_only_to_admin_and_owner(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('proofs/bukti.jpg', 'x');
        $order = Order::factory()->qris()->create(['payment_proof' => 'proofs/bukti.jpg']);

        $this->get('/media/proofs/bukti.jpg')->assertNotFound();
        $this->actingAs(User::factory()->create())->get('/media/proofs/bukti.jpg')->assertNotFound();
        $this->actingAs($order->user)->get('/media/proofs/bukti.jpg')->assertOk();
        $this->actingAs($this->admin)->get('/media/proofs/bukti.jpg')->assertOk();
    }

    public function test_product_photo_stays_public(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/a.jpg', 'x');

        $this->get('/media/products/a.jpg')->assertOk();
    }

    public function test_media_rejects_path_traversal(): void
    {
        $this->get('/media/products/../../.env')->assertNotFound();
    }

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), ['email' => 'x@y.test', 'password' => 'salah']);
        }

        $this->post(route('login'), ['email' => 'x@y.test', 'password' => 'salah'])->assertStatus(429);
    }

    public function test_qris_order_cannot_be_marked_paid_without_proof(): void
    {
        $order = Order::factory()->qris()->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update', $order), ['status' => 'processing', 'payment_status' => 'paid'])
            ->assertSessionHas('error');

        $this->assertSame('unpaid', $order->fresh()->payment_status);

        $order->update(['payment_proof' => 'proofs/bukti.jpg']);

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update', $order), ['status' => 'processing', 'payment_status' => 'paid'])
            ->assertSessionHas('success');

        $this->assertSame('paid', $order->fresh()->payment_status);
    }

    public function test_logo_and_hero_are_fixed_files_with_exact_filename_case(): void
    {
        $this->assertFileExists(public_path('images/logo.png'));
        $this->assertFileExists(public_path('images/bg.jpg'));
        $this->get('/')->assertOk()->assertSee('images/bg.jpg', false)->assertSee('images/logo.png', false);
    }
}
