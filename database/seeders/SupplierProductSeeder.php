<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Supplier;

class SupplierProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::all()->each(function (Product $product) {
            $supplierIds = Supplier::inRandomOrder()
                ->limit(fake()->numberBetween(1, 3))
                ->pluck('id');

            $product->suppliers()->attach($supplierIds);
        });
    }
}
