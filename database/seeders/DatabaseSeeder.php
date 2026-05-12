<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@otphub.local'],
            [
                'name' => 'Platform Admin',
                'password' => Hash::make('Admin@12345'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'merchant@otphub.local'],
            [
                'name' => 'Demo Merchant',
                'password' => Hash::make('Merchant@12345'),
                'role' => User::ROLE_MERCHANT,
                'is_active' => true,
                'subscription_start' => now()->subDay()->toDateString(),
                'subscription_end' => now()->addYear()->toDateString(),
            ]
        );
    }
}
