<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CafeSettingSeeder::class,
            CustomerSeeder::class,
            TableSeeder::class,
            InventorySeeder::class,
            MenuSeeder::class,
            HRSeeder::class,
            OrderSeeder::class,
            ExpenseSeeder::class,
            AtsSeeder::class,
        ]);
    }
}
