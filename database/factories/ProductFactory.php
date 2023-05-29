<?php

namespace Database\Factories;

use App\Models\Category;
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
        return [
            'sku' => $this->faker->uuid,
            'name' => $this->faker->text(50),
            'price' => $this->faker->numberBetween(1,200),
            'amount' => $this->faker->numberBetween(0, 100),
            'description' => $this->faker->text(),
            'additional_info' => $this->faker->text(),
            'avg_rating' => $this->faker->randomFloat(2,3.1,3.9),
            'tags' => ['tag1', 'tag2'],
            'category' => Category::factory(),
        ];
    }
}
