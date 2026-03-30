<?php

namespace App\Services;

use App\Models\SupplierProduct;

class SupplierProductService extends BaseCrudService
{
    protected function model(): string
    {
        return SupplierProduct::class;
    }

    protected array $with = ['supplier', 'product'];
}
