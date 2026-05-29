<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ClientSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            SupplierProductSeeder::class,
            OrderSeeder::class,
            orderDetailSeeder::class,
        ]);
    }
}
