<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierProductFactory extends Factory
{
    protected $model = SupplierProduct::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'product_id'  => Product::factory(),
        ];
    }
}
