<?php

namespace App\Services;

use App\Models\OrderDetail;

class OrderDetailService extends BaseCrudService
{
    protected function model(): string
    {
        return OrderDetail::class;
    }

    protected array $with = ['order', 'product'];
}
