<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . fake()->unique()->numerify('######'),
            'status_id'    => fake()->numberBetween(1, 5),
            'client_id'    => Client::factory(),
        ];
    }
}
