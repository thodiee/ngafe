<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Ahmad Hidayat',
                'phone' => '081234567890',
                'address' => 'Jl. Sudirman No. 12, Jakarta Pusat',
                'dob' => '1990-05-15',
                'points' => 1500,
            ],
            [
                'name' => 'Dewi Lestari',
                'phone' => '082345678901',
                'address' => 'Jl. Thamrin No. 88, Menteng, Jakarta Pusat',
                'dob' => '1993-11-20',
                'points' => 850,
            ],
            [
                'name' => 'Budi Santoso',
                'phone' => '083456789012',
                'address' => 'Jl. Kebon Jeruk No. 5A, Jakarta Barat',
                'dob' => '1988-08-05',
                'points' => 3200,
            ],
            [
                'name' => 'Siti Aminah',
                'phone' => '084567890123',
                'address' => 'Jl. Gatot Subroto No. 44, Jakarta Selatan',
                'dob' => '1995-02-14',
                'points' => 450,
            ],
            [
                'name' => 'Rian Wijaya',
                'phone' => '085678901234',
                'address' => 'Jl. Melawai Raya No. 100, Kebayoran Baru, Jakarta Selatan',
                'dob' => '1992-06-12',
                'points' => 2100,
            ],
            [
                'name' => 'Indah Putri',
                'phone' => '086789012345',
                'address' => 'Jl. Dago No. 123, Bandung',
                'dob' => '1996-09-30',
                'points' => 120,
            ],
            [
                'name' => 'Joko Widodo',
                'phone' => '087890123456',
                'address' => 'Jl. Slamet Riyadi No. 4, Surakarta',
                'dob' => '1961-06-21',
                'points' => 5000,
            ],
            [
                'name' => 'Megaawati Sukarni',
                'phone' => '088901234567',
                'address' => 'Jl. Kebagusan No. 12, Jakarta Selatan',
                'dob' => '1985-01-23',
                'points' => 750,
            ],
            [
                'name' => 'Prabowo Subi',
                'phone' => '089012345678',
                'address' => 'Jl. Kertanegara No. 8, Jakarta Selatan',
                'dob' => '1987-10-17',
                'points' => 4200,
            ],
            [
                'name' => 'Anies Baswedan',
                'phone' => '081112233445',
                'address' => 'Jl. Lebak Bulus Dalam No. 2, Jakarta Selatan',
                'dob' => '1989-05-07',
                'points' => 1800,
            ],
            [
                'name' => 'Ganjar Pranowo',
                'phone' => '082223344556',
                'address' => 'Jl. S. Parman No. 56, Semarang',
                'dob' => '1986-10-28',
                'points' => 950,
            ],
            [
                'name' => 'Gibran Rakabuming',
                'phone' => '083334455667',
                'address' => 'Jl. Ahmad Yani No. 12, Surakarta',
                'dob' => '1987-10-01',
                'points' => 1500,
            ],
        ];

        foreach ($customers as $cust) {
            Customer::updateOrCreate(['phone' => $cust['phone']], $cust);
        }
    }
}
