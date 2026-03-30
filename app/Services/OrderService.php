<?php

namespace App\Services;

use App\Models\Order;

class OrderService extends BaseCrudService
{
    protected function model(): string
    {
        return Order::class;
    }

    protected array $with = ['client', 'orderDetails.product'];
}
