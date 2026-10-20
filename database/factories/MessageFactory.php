<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * Pesan dari customer ke toko (sender = pemilik percakapan).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'sender_id' => fn (array $attrs) => $attrs['customer_id'],
            'body' => fake()->sentence(),
        ];
    }
}
