<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = array_map(
            fn (string $name) => ['name' => $name],
            Status::NAMES
        );

        Status::factory()
            ->count(count(Status::NAMES))
            ->sequence(...$states)
            ->create();
    }
}
