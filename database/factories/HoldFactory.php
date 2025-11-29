<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;


class HoldFactory extends Factory
{
   
    public function definition(): array
    {
        
        $product = Product::inRandomOrder()->first();

    
        $productId = $product ? $product->id : 1;

    
        $qtyToHold = $this->faker->numberBetween(1, 10);

       
        $expiresAt = Carbon::now()->addMinutes($this->faker->numberBetween(1, 5));

        return [
            'product_id' => $productId,
            'qty' => $qtyToHold,
            'expires_at' => $expiresAt,
        ];
    }
}