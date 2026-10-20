<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = ['rating' => 5, 'body' => 'Kue susnya enak sekali.'];

    public function test_guest_cannot_post_review(): void
    {
        $this->post(route('reviews.store'), $this->payload)->assertRedirect(route('login'));
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_customer_without_order_cannot_post_review(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('reviews.store'), $this->payload)->assertSessionHas('error');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_customer_with_unfinished_order_cannot_post_review(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->status('processing')->create();
        Order::factory()->for($user)->status('cancelled')->create();

        $this->actingAs($user)->post(route('reviews.store'), $this->payload)->assertSessionHas('error');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_buyer_with_finished_order_can_post_review(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->status('delivered')->create();

        $this->actingAs($user)->post(route('reviews.store'), $this->payload)->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', ['user_id' => $user->id, 'rating' => 5]);
    }

    public function test_admin_cannot_post_review(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post(route('reviews.store'), $this->payload)
            ->assertForbidden();

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_form_is_hidden_for_non_buyer_and_shown_for_buyer(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/')
            ->assertSee('pesanannya sudah selesai')
            ->assertDontSee('Kirim komentar');

        Order::factory()->for($user)->status('delivered')->create();

        $this->actingAs($user)->get('/')->assertSee('Kirim komentar');
    }
}
