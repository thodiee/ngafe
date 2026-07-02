<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $financeUser = User::where('role', 'finance')->first() ?? User::where('role', 'admin')->first();
        $userId = $financeUser ? $financeUser->id : null;

        $categories = [
            'Bahan Baku' => ['Pembelian kopi darurat', 'Beli buah segar tambahan', 'Pembelian es batu kristal', 'Bahan baku mentega habis'],
            'Utilitas' => ['Bayar Listrik PLN', 'Bayar Tagihan Air PDAM', 'Tagihan Internet Wi-Fi Biznet'],
            'Pemasaran' => ['Cetak Brosur Promo', 'Iklan Instagram Ads', 'Cetak Banner Menu Depan'],
            'Gaji & Staf' => ['Pembelian seragam baru staf', 'Snack bulanan ruang istirahat staf'],
            'Pemeliharaan' => ['Servis mesin espresso', 'Perbaikan AC area indoor', 'Pembelian lampu bohlam area outdoor'],
            'Lain-lain' => ['Beli tisu meja makan', 'Beli sabun cuci piring dapur', 'Beli kantong plastik takeaway']
        ];

        // Seed 15 expenses spread across the last 30 days
        for ($i = 1; $i <= 15; $i++) {
            $expenseDate = $now->copy()->subDays(rand(1, 28));
            $categoryKeys = array_keys($categories);
            $category = $categoryKeys[rand(0, count($categoryKeys) - 1)];
            
            $noteList = $categories[$category];
            $notes = $noteList[rand(0, count($noteList) - 1)];

            // Assign amounts depending on category
            if ($category === 'Utilitas') {
                $amount = rand(500000, 2500000);
            } elseif ($category === 'Pemeliharaan') {
                $amount = rand(200000, 1200000);
            } elseif ($category === 'Bahan Baku') {
                $amount = rand(100000, 800000);
            } elseif ($category === 'Pemasaran') {
                $amount = rand(250000, 1500000);
            } else {
                $amount = rand(50000, 300000);
            }

            Expense::create([
                'expense_date' => $expenseDate->toDateString(),
                'category' => $category,
                'amount' => $amount,
                'notes' => $notes,
                'user_id' => $userId,
                'created_at' => $expenseDate,
                'updated_at' => $expenseDate,
            ]);
        }
    }
}
