<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;

class OrderDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::all()->each(function (Order $order) {
            Product::inRandomOrder()
                ->limit(fake()->numberBetween(1, 5))
                ->get()
                ->each(function (Product $product) use ($order) {
                    OrderDetail::factory()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => fake()->numberBetween(1, 5),
                        'price' => $product->price,
                    ]);
                });
        });
    }
}
