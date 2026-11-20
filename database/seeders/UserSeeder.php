<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        User::updateOrCreate(
            ['email' => 'admin@tripmate.com'],
            [
                'name' => 'Administrator TripMate',
                'password' => Hash::make('password'),
                'phone' => '081200000001',
                'role' => UserRole::Admin,
                'is_active' => true,
            ]
        );

        // 2. Staff
        User::updateOrCreate(
            ['email' => 'staff@tripmate.com'],
            [
                'name' => 'Staff Operasional',
                'password' => Hash::make('password'),
                'phone' => '081200000002',
                'role' => UserRole::Staff,
                'is_active' => true,
            ]
        );

        // 3. Customers
        User::updateOrCreate(
            ['email' => 'customer@tripmate.com'],
            [
                'name' => 'Andi Pratama',
                'password' => Hash::make('password'),
                'phone' => '081298765432',
                'role' => UserRole::Customer,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'budi@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'phone' => '081311223344',
                'role' => UserRole::Customer,
                'is_active' => true,
            ]
        );
    }
}
