<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\PaymentMethod;
use App\Models\Setting;

class CafeSettingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Units
        $units = [
            ['name' => 'Gram', 'abbreviation' => 'gr'],
            ['name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['name' => 'Mililiter', 'abbreviation' => 'ml'],
            ['name' => 'Liter', 'abbreviation' => 'L'],
            ['name' => 'Pieces', 'abbreviation' => 'pcs'],
            ['name' => 'Pack', 'abbreviation' => 'pack'],
            ['name' => 'Box', 'abbreviation' => 'box'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(['abbreviation' => $unit['abbreviation']], $unit);
        }

        // 2. Payment Methods
        $paymentMethods = [
            [
                'name' => 'Cash',
                'icon' => 'fa-wallet',
                'is_active' => true,
                'has_surcharge' => false,
                'surcharge_rate' => 0
            ],
            [
                'name' => 'QRIS Gateway',
                'icon' => 'fa-qrcode',
                'is_active' => true,
                'has_surcharge' => false,
                'surcharge_rate' => 0
            ],
            [
                'name' => 'Debit Card',
                'icon' => 'fa-credit-card',
                'is_active' => true,
                'has_surcharge' => true,
                'surcharge_rate' => 1.5
            ],
            [
                'name' => 'Bank Transfer',
                'icon' => 'fa-money-bill-transfer',
                'is_active' => true,
                'has_surcharge' => false,
                'surcharge_rate' => 0
            ]
        ];

        foreach ($paymentMethods as $pm) {
            PaymentMethod::updateOrCreate(['name' => $pm['name']], $pm);
        }

        // 3. System Settings
        $settings = [
            ['key' => 'loyalty_points_multiplier', 'value' => '0.001', 'type' => 'float'],
            ['key' => 'loyalty_points_to_cash', 'value' => '10', 'type' => 'int'],
            ['key' => 'birthday_discount_percent', 'value' => '10', 'type' => 'int'],
            ['key' => 'attendance_grace_period', 'value' => '15', 'type' => 'int'],
            ['key' => 'attendance_lat_long', 'value' => '-6.200000,106.816666', 'type' => 'string'],
            ['key' => 'tax_rate', 'value' => '11', 'type' => 'int'],
            ['key' => 'service_charge', 'value' => '5', 'type' => 'int'],
            ['key' => 'cafe_name', 'value' => 'Ngafe Premium Lounge', 'type' => 'string'],
            ['key' => 'cafe_address', 'value' => 'Jl. Kemang Raya No. 45, Jakarta Selatan', 'type' => 'string'],
            ['key' => 'cafe_phone', 'value' => '021-7890123', 'type' => 'string'],
            ['key' => 'cafe_email', 'value' => 'info@ngafe.com', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
