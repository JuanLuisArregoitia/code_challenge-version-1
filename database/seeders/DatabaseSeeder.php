<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('secret');

        for ($i = 1; $i <= 10; $i++) {
            $number = str_pad($i, 3, '0', STR_PAD_LEFT);

            User::factory()->create([
                'name'              => "Test User {$number}",
                'email'             => "test{$number}@test.com",
                'password'          => $password,
                'email_verified_at' => now(),
            ]);
        }
    }
}
