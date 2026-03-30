<?php

namespace App\Services;

use App\Models\Supplier;

class SupplierService extends BaseCrudService
{
    protected function model(): string
    {
        return Supplier::class;
    }
}
