<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'stock' => $this->faker->numberBetween(0, 500), 
            'price' => $this->faker->randomFloat(2, 10, 999.99), 
        ];
    }
}