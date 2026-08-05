<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $title = $this->faker->words(3, true) . ' ' . $this->faker->randomElement(['Pack', 'Bottle', 'Box', '500g', '1L', '1kg']);
        $price = $this->faker->randomFloat(2, 20, 1500);
        $hasDiscount = $this->faker->boolean(40);

        return [
            'title' => ucwords($title),
            'slug' => Str::slug($title . '-' . $this->faker->unique()->randomNumber(4)),
            'description' => $this->faker->sentence(12),
            'price' => $price,
            'sale_price' => $hasDiscount ? round($price * 0.85, 2) : null,
            'stock_quantity' => $this->faker->numberBetween(10, 200),
            'sku' => strtoupper(Str::random(3)) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'prescription_required' => $this->faker->boolean(10), // 10% chance
            'is_active' => true,
        ];
    }
}