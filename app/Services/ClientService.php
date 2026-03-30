<?php

namespace App\Services;

use App\Models\Client;

class ClientService extends BaseCrudService
{
    protected function model(): string
    {
        return Client::class;
    }
}
