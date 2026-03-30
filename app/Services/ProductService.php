<?php

namespace App\Services;

use App\Models\Product;

class ProductService extends BaseCrudService
{
    protected function model(): string
    {
        return Product::class;
    }
}
