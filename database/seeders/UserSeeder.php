<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Ngafe',
                'email' => 'admin@ngafe.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'base_salary' => 8500000,
                'daily_allowance' => 50000,
                'lateness_penalty' => 25000,
            ],
            [
                'name' => 'Owner Ngafe',
                'email' => 'owner@ngafe.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'base_salary' => 0,
                'daily_allowance' => 0,
                'lateness_penalty' => 0,
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@ngafe.com',
                'password' => Hash::make('password'),
                'role' => 'hr',
                'base_salary' => 7000000,
                'daily_allowance' => 45000,
                'lateness_penalty' => 20000,
            ],
            [
                'name' => 'Cashier Sarah',
                'email' => 'cashier@ngafe.com',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'base_salary' => 4500000,
                'daily_allowance' => 30000,
                'lateness_penalty' => 15000,
            ],
            [
                'name' => 'Cashier Budi',
                'email' => 'cashier2@ngafe.com',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'base_salary' => 4500000,
                'daily_allowance' => 30000,
                'lateness_penalty' => 15000,
            ],
            [
                'name' => 'Kitchen Chef Adi',
                'email' => 'kitchen@ngafe.com',
                'password' => Hash::make('password'),
                'role' => 'kitchen',
                'base_salary' => 5000000,
                'daily_allowance' => 35000,
                'lateness_penalty' => 15000,
            ],
            [
                'name' => 'Waiter Rian',
                'email' => 'waiter@ngafe.com',
                'password' => Hash::make('password'),
                'role' => 'waiter',
                'base_salary' => 4000000,
                'daily_allowance' => 30000,
                'lateness_penalty' => 15000,
            ],
            [
                'name' => 'Waiter Sinta',
                'email' => 'waiter2@ngafe.com',
                'password' => Hash::make('password'),
                'role' => 'waiter',
                'base_salary' => 4000000,
                'daily_allowance' => 30000,
                'lateness_penalty' => 15000,
            ],
            [
                'name' => 'Inventory Staff',
                'email' => 'inventory@ngafe.com',
                'password' => Hash::make('password'),
                'role' => 'inventory',
                'base_salary' => 4800000,
                'daily_allowance' => 30000,
                'lateness_penalty' => 15000,
            ],
            [
                'name' => 'Finance Manager',
                'email' => 'finance@ngafe.com',
                'password' => Hash::make('password'),
                'role' => 'finance',
                'base_salary' => 6500000,
                'daily_allowance' => 40000,
                'lateness_penalty' => 20000,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }
    }
}
