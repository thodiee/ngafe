<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;
use App\Models\Table;
use App\Models\Reservation;
use Carbon\Carbon;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Areas
        $areas = [
            ['name' => 'Indoor AC'],
            ['name' => 'Outdoor Garden'],
            ['name' => 'Rooftop Lounge'],
            ['name' => 'VIP Room'],
        ];

        $areaModels = [];
        foreach ($areas as $area) {
            $areaModels[$area['name']] = Area::updateOrCreate(['name' => $area['name']], $area);
        }

        // 2. Tables (T01 - T20)
        $statuses = ['empty', 'occupied', 'empty', 'empty', 'reserved']; // weighted towards empty
        $tableModels = [];
        
        for ($i = 1; $i <= 20; $i++) {
            $numStr = 'T' . str_pad($i, 2, '0', STR_PAD_LEFT);
            
            // Distribute areas
            if ($i <= 6) {
                $areaId = $areaModels['Indoor AC']->id;
            } elseif ($i <= 12) {
                $areaId = $areaModels['Outdoor Garden']->id;
            } elseif ($i <= 16) {
                $areaId = $areaModels['Rooftop Lounge']->id;
            } else {
                $areaId = $areaModels['VIP Room']->id;
            }

            // Simple status distribution
            $status = $statuses[$i % count($statuses)];
            if ($i === 1 || $i === 2) {
                $status = 'occupied'; // Ensure some occupied tables for testing waiter/kitchen
            }

            $table = Table::updateOrCreate(
                ['table_number' => $numStr],
                [
                    'area_id' => $areaId,
                    'qr_token' => 'qr_' . uniqid(),
                    'status' => $status,
                ]
            );
            $tableModels[] = $table;
        }

        // 3. Reservations (Reservasi hari ini, besok, dan kemarin)
        $now = Carbon::now();
        
        $reservations = [
            // Yesterday (Completed/Cancelled)
            [
                'table_id' => $tableModels[0]->id, // T01
                'customer_name' => 'Ahmad Hidayat',
                'customer_phone' => '081234567890',
                'reservation_time' => $now->copy()->subDay()->setTime(18, 0, 0),
                'status' => 'completed',
            ],
            [
                'table_id' => $tableModels[1]->id, // T02
                'customer_name' => 'Dewi Lestari',
                'customer_phone' => '082345678901',
                'reservation_time' => $now->copy()->subDay()->setTime(19, 30, 0),
                'status' => 'cancelled',
            ],
            // Today (Pending / Confirmed)
            [
                'table_id' => $tableModels[4]->id, // T05 (reserved status)
                'customer_name' => 'Budi Santoso',
                'customer_phone' => '083456789012',
                'reservation_time' => $now->copy()->setTime(19, 0, 0),
                'status' => 'confirmed',
            ],
            [
                'table_id' => $tableModels[9]->id, // T10
                'customer_name' => 'Siti Aminah',
                'customer_phone' => '084567890123',
                'reservation_time' => $now->copy()->setTime(20, 0, 0),
                'status' => 'pending',
            ],
            // Tomorrow (Pending)
            [
                'table_id' => $tableModels[14]->id, // T15
                'customer_name' => 'Rian Wijaya',
                'customer_phone' => '085678901234',
                'reservation_time' => $now->copy()->addDay()->setTime(17, 30, 0),
                'status' => 'pending',
            ],
            [
                'table_id' => $tableModels[18]->id, // T19
                'customer_name' => 'Indah Putri',
                'customer_phone' => '086789012345',
                'reservation_time' => $now->copy()->addDay()->setTime(21, 0, 0),
                'status' => 'confirmed',
            ],
        ];

        foreach ($reservations as $r) {
            Reservation::create($r);
        }
    }
}
