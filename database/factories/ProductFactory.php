<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->word(),
            'description' => fake()->sentence(),
            'price'       => fake()->randomFloat(2, 1, 9999),
            'quantity'    => fake()->numberBetween(0, 1000),
        ];
    }
}
