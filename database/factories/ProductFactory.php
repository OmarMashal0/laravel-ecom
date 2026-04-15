<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        $name = ucwords($name);
        $price = $this->faker->randomFloat(2, 10, 500);
        $comparePrice = $this->faker->boolean(30) ? $price * $this->faker->randomFloat(2, 1.1, 1.5) : null;

        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'brand_id' => $this->faker->boolean(80) ? (Brand::inRandomOrder()->first()?->id ?? Brand::factory()) : null,
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'short_description' => $this->faker->sentence(15),
            'description' => '<p>' . $this->faker->paragraph(10) . '</p><p>' . $this->faker->paragraph(8) . '</p>',
            'price' => $price,
            'compare_price' => $comparePrice,
            'cost_price' => $price * 0.6,
            'stock_quantity' => $this->faker->numberBetween(0, 500),
            'low_stock_threshold' => 10,
            'manage_stock' => true,
            'stock_status' => $this->faker->randomElement(['in_stock', 'in_stock', 'in_stock', 'out_of_stock']),
            'is_active' => $this->faker->boolean(95),
            'is_featured' => $this->faker->boolean(20),
            'has_variants' => $this->faker->boolean(30),
            'weight' => $this->faker->randomFloat(2, 0.1, 50),
            'meta_title' => $name,
            'meta_description' => $this->faker->sentence(20),
            'views_count' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
