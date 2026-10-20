<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code' => 'SUS-'.strtoupper(Str::random(6)),
            'customer_name' => fake()->name(),
            'phone' => '0812'.fake()->numerify('########'),
            'address' => fake()->address(),
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'new',
            'total' => 10000,
        ];
    }

    public function qris(): static
    {
        return $this->state(fn () => ['payment_method' => 'qris']);
    }

    public function status(string $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
