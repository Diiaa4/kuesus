<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Kue Sus '.fake()->unique()->word(),
            'description' => fake()->sentence(),
            'price' => fake()->numberBetween(3, 50) * 1000,
            'stock' => 30,
            'is_active' => true,
        ];
    }

    public function lowStock(int $stock = 3): static
    {
        return $this->state(fn () => ['stock' => $stock]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
