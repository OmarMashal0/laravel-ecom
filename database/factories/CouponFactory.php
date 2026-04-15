<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['fixed', 'percentage']);
        $value = $type === 'percentage' ? $this->faker->numberBetween(5, 50) : $this->faker->numberBetween(5, 100);

        return [
            'code' => strtoupper(Str::random(8)),
            'type' => $type,
            'value' => $value,
            'minimum_order_value' => $this->faker->boolean(50) ? $this->faker->numberBetween(50, 200) : null,
            'maximum_discount' => $type === 'percentage' ? $this->faker->numberBetween(20, 100) : null,
            'usage_limit' => $this->faker->boolean(70) ? $this->faker->numberBetween(10, 100) : null,
            'usage_limit_per_customer' => $this->faker->boolean(80) ? $this->faker->numberBetween(1, 5) : null,
            'starts_at' => now()->subDays($this->faker->numberBetween(0, 30)),
            'expires_at' => now()->addDays($this->faker->numberBetween(30, 90)),
            'is_active' => true,
        ];
    }
}
