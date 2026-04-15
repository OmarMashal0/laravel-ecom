<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $color = $this->faker->randomElement(['Red', 'Blue', 'Green', 'Black', 'White', 'Yellow']);
        $size = $this->faker->randomElement(['Small', 'Medium', 'Large', 'XL', 'XXL']);
        $name = "$color - $size";
        $price = $this->faker->randomFloat(2, 15, 600);

        return [
            'product_id' => Product::factory(),
            'sku' => 'VAR-' . strtoupper(Str::random(8)),
            'name' => $name,
            'options' => json_encode(['color' => $color, 'size' => $size]),
            'price' => $price,
            'compare_price' => $this->faker->boolean(30) ? $price * 1.3 : null,
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'stock_status' => $this->faker->randomElement(['in_stock', 'in_stock', 'out_of_stock']),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
