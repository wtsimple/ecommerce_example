<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->numberBetween(1,300);
        return [
            'user_id' => User::factory(),
            'sku' => function() use ($price) {
                return Product::factory()->createOne(['price' => $price])->sku;
            },
            'amount' => 1,
            'total_paid' => $price,
        ];
    }
}
