<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\IngredientBatch;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Waste;
use App\Models\WasteItem;
use App\Models\StockMutation;
use App\Models\User;
use Carbon\Carbon;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $inventoryUser = User::where('role', 'inventory')->first() ?? User::where('role', 'admin')->first();
        $userId = $inventoryUser ? $inventoryUser->id : null;

        // 1. Suppliers
        $suppliers = [
            [
                'name' => 'PT. Java Coffee Beans',
                'contact_person' => 'Hendra Wijaya',
                'phone' => '081122334455',
                'email' => 'sales@javacoffee.com',
                'address' => 'Kawasan Industri Candi Blok C No. 5, Semarang',
            ],
            [
                'name' => 'CV. Sembako Makmur Jaya',
                'contact_person' => 'Ibu Hartati',
                'phone' => '081234567890',
                'email' => 'hartati@sembakomakmur.co.id',
                'address' => 'Pasar Induk Kramat Jati Ruko No. 12, Jakarta Timur',
            ],
            [
                'name' => 'UD. Tani Segar Nusantara',
                'contact_person' => 'Agus Setiawan',
                'phone' => '087788990011',
                'email' => 'info@tanisegar.com',
                'address' => 'Jl. Kaliurang KM 10, Sleman, Yogyakarta',
            ],
        ];

        $supplierModels = [];
        foreach ($suppliers as $sup) {
            $supplierModels[] = Supplier::create($sup);
        }

        // 2. Ingredients
        $ingredients = [
            ['name' => 'Biji Kopi Arabica', 'stock_gudang' => 15000, 'stock_dapur' => 2000, 'unit' => 'gr', 'minimum_stock' => 5000],
            ['name' => 'Biji Kopi Robusta', 'stock_gudang' => 10000, 'stock_dapur' => 1500, 'unit' => 'gr', 'minimum_stock' => 3000],
            ['name' => 'Susu Segar UHT', 'stock_gudang' => 48000, 'stock_dapur' => 6000, 'unit' => 'ml', 'minimum_stock' => 12000],
            ['name' => 'Gula Aren Cair', 'stock_gudang' => 10000, 'stock_dapur' => 1000, 'unit' => 'gr', 'minimum_stock' => 2000],
            ['name' => 'Daun Teh Premium', 'stock_gudang' => 5000, 'stock_dapur' => 800, 'unit' => 'gr', 'minimum_stock' => 1500],
            ['name' => 'Tepung Terigu', 'stock_gudang' => 25000, 'stock_dapur' => 3000, 'unit' => 'gr', 'minimum_stock' => 5000],
            ['name' => 'Mentega Margarin', 'stock_gudang' => 8000, 'stock_dapur' => 1000, 'unit' => 'gr', 'minimum_stock' => 2000],
            ['name' => 'Telur Ayam', 'stock_gudang' => 150, 'stock_dapur' => 30, 'unit' => 'pcs', 'minimum_stock' => 50],
            ['name' => 'Beras Cianjur', 'stock_gudang' => 50000, 'stock_dapur' => 5000, 'unit' => 'gr', 'minimum_stock' => 10000],
            ['name' => 'Daging Ayam Fillet', 'stock_gudang' => 12000, 'stock_dapur' => 2000, 'unit' => 'gr', 'minimum_stock' => 3000],
            ['name' => 'Sirup Caramel', 'stock_gudang' => 6000, 'stock_dapur' => 1000, 'unit' => 'ml', 'minimum_stock' => 2000],
            ['name' => 'Cokelat Bubuk', 'stock_gudang' => 4000, 'stock_dapur' => 500, 'unit' => 'gr', 'minimum_stock' => 1000],
            ['name' => 'Roti Tawar Kupas', 'stock_gudang' => 20, 'stock_dapur' => 5, 'unit' => 'pcs', 'minimum_stock' => 10],
        ];

        $ingredientModels = [];
        foreach ($ingredients as $ing) {
            $ingredientModels[] = Ingredient::create($ing);
        }

        // 3. Purchase Orders (Draft, Sent, Completed, Cancelled)
        $poData = [
            // PO 1: Completed
            [
                'po_number' => 'PO-' . $now->copy()->subDays(10)->format('Ymd') . '-001',
                'supplier_id' => $supplierModels[0]->id, // Java Coffee Beans
                'po_date' => $now->copy()->subDays(10)->toDateString(),
                'expected_date' => $now->copy()->subDays(8)->toDateString(),
                'status' => 'completed',
                'total_amount' => 1650000,
                'notes' => 'Pemesanan kopi rutin bulanan.',
                'items' => [
                    ['ingredient_id' => $ingredientModels[0]->id, 'quantity' => 10000, 'unit_price' => 120, 'subtotal' => 1200000], // 10kg Arabica
                    ['ingredient_id' => $ingredientModels[1]->id, 'quantity' => 5000, 'unit_price' => 90, 'subtotal' => 450000], // 5kg Robusta
                ]
            ],
            // PO 2: Partial
            [
                'po_number' => 'PO-' . $now->copy()->subDays(5)->format('Ymd') . '-002',
                'supplier_id' => $supplierModels[1]->id, // Sembako Makmur
                'po_date' => $now->copy()->subDays(5)->toDateString(),
                'expected_date' => $now->copy()->subDays(3)->toDateString(),
                'status' => 'partial',
                'total_amount' => 1140000,
                'notes' => 'Pemesanan susu dan sembako.',
                'items' => [
                    ['ingredient_id' => $ingredientModels[2]->id, 'quantity' => 48000, 'unit_price' => 18, 'subtotal' => 864000], // 48L Susu
                    ['ingredient_id' => $ingredientModels[8]->id, 'quantity' => 50000, 'unit_price' => 5.5, 'subtotal' => 276000], // 50kg Beras
                ]
            ],
            // PO 3: Sent
            [
                'po_number' => 'PO-' . $now->copy()->subDays(2)->format('Ymd') . '-003',
                'supplier_id' => $supplierModels[2]->id, // Tani Segar
                'po_date' => $now->copy()->subDays(2)->toDateString(),
                'expected_date' => $now->copy()->addDays(1)->toDateString(),
                'status' => 'sent',
                'total_amount' => 360000,
                'notes' => 'Pemesanan daging ayam fillet segar.',
                'items' => [
                    ['ingredient_id' => $ingredientModels[9]->id, 'quantity' => 6000, 'unit_price' => 60, 'subtotal' => 360000], // 6kg Ayam
                ]
            ],
            // PO 4: Draft
            [
                'po_number' => 'PO-' . $now->format('Ymd') . '-004',
                'supplier_id' => $supplierModels[1]->id, // Sembako Makmur
                'po_date' => $now->toDateString(),
                'expected_date' => $now->copy()->addDays(3)->toDateString(),
                'status' => 'draft',
                'total_amount' => 500000,
                'notes' => 'Draft pemesanan terigu dan gula.',
                'items' => [
                    ['ingredient_id' => $ingredientModels[5]->id, 'quantity' => 25000, 'unit_price' => 12, 'subtotal' => 300000],
                    ['ingredient_id' => $ingredientModels[3]->id, 'quantity' => 10000, 'unit_price' => 20, 'subtotal' => 200000],
                ]
            ]
        ];

        foreach ($poData as $po) {
            $items = $po['items'];
            unset($po['items']);
            
            $poModel = PurchaseOrder::create($po);
            foreach ($items as $item) {
                $item['purchase_order_id'] = $poModel->id;
                PurchaseOrderItem::create($item);
            }

            // 4. Goods Receipts (linked to Completed / Partial POs)
            if ($poModel->status === 'completed') {
                $gr = GoodsReceipt::create([
                    'gr_number' => 'GR-' . Carbon::parse($poModel->po_date)->addDays(2)->format('Ymd') . '-001',
                    'purchase_order_id' => $poModel->id,
                    'supplier_id' => $poModel->supplier_id,
                    'receipt_date' => Carbon::parse($poModel->po_date)->addDays(2)->toDateString(),
                    'notes' => 'Kopi diterima dengan kondisi baik.',
                ]);

                foreach ($items as $item) {
                    $grItem = GoodsReceiptItem::create([
                        'goods_receipt_id' => $gr->id,
                        'ingredient_id' => $item['ingredient_id'],
                        'quantity_received' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                    ]);

                    // 5. Ingredient Batches (FIFO)
                    IngredientBatch::create([
                        'ingredient_id' => $item['ingredient_id'],
                        'goods_receipt_item_id' => $grItem->id,
                        'initial_quantity' => $item['quantity'],
                        'current_quantity' => $item['quantity'] - 500, // assume some consumed
                        'unit_price' => $item['unit_price'],
                        'batch_date' => $gr->receipt_date,
                        'expiry_date' => Carbon::parse($gr->receipt_date)->addMonths(6)->toDateString(),
                    ]);

                    // Mutation
                    StockMutation::create([
                        'ingredient_id' => $item['ingredient_id'],
                        'location' => 'gudang',
                        'type' => 'in',
                        'quantity' => $item['quantity'],
                        'reference_type' => 'GoodsReceipt',
                        'reference_id' => $gr->id,
                        'notes' => 'Penerimaan barang dari PO: ' . $poModel->po_number,
                    ]);
                }
            } elseif ($poModel->status === 'partial') {
                $gr = GoodsReceipt::create([
                    'gr_number' => 'GR-' . Carbon::parse($poModel->po_date)->addDays(2)->format('Ymd') . '-002',
                    'purchase_order_id' => $poModel->id,
                    'supplier_id' => $poModel->supplier_id,
                    'receipt_date' => Carbon::parse($poModel->po_date)->addDays(2)->toDateString(),
                    'notes' => 'Baru diterima sebagian (Susu Segar).',
                ]);

                // Receive only the first item (Susu)
                $susuItem = $items[0];
                $grItem = GoodsReceiptItem::create([
                    'goods_receipt_id' => $gr->id,
                    'ingredient_id' => $susuItem['ingredient_id'],
                    'quantity_received' => $susuItem['quantity'],
                    'unit_price' => $susuItem['unit_price'],
                ]);

                IngredientBatch::create([
                    'ingredient_id' => $susuItem['ingredient_id'],
                    'goods_receipt_item_id' => $grItem->id,
                    'initial_quantity' => $susuItem['quantity'],
                    'current_quantity' => $susuItem['quantity'] - 2000,
                    'unit_price' => $susuItem['unit_price'],
                    'batch_date' => $gr->receipt_date,
                    'expiry_date' => Carbon::parse($gr->receipt_date)->addWeeks(3)->toDateString(),
                ]);

                StockMutation::create([
                    'ingredient_id' => $susuItem['ingredient_id'],
                    'location' => 'gudang',
                    'type' => 'in',
                    'quantity' => $susuItem['quantity'],
                    'reference_type' => 'GoodsReceipt',
                    'reference_id' => $gr->id,
                    'notes' => 'Penerimaan Parsial PO: ' . $poModel->po_number,
                ]);
            }
        }

        // 6. Stock Transfers (Transfer dari gudang ke dapur)
        $transfer = StockTransfer::create([
            'transfer_number' => 'TRF-' . $now->copy()->subDays(1)->format('Ymd') . '-001',
            'transfer_date' => $now->copy()->subDays(1)->toDateString(),
            'notes' => 'Stok harian dapur.',
            'user_id' => $userId,
        ]);

        $transfers = [
            ['ingredient_id' => $ingredientModels[0]->id, 'quantity' => 1000], // 1kg Arabica
            ['ingredient_id' => $ingredientModels[2]->id, 'quantity' => 5000], // 5L Susu
            ['ingredient_id' => $ingredientModels[3]->id, 'quantity' => 500],  // 500g Gula Aren
        ];

        foreach ($transfers as $item) {
            $item['stock_transfer_id'] = $transfer->id;
            StockTransferItem::create($item);

            // Mutation out of gudang
            StockMutation::create([
                'ingredient_id' => $item['ingredient_id'],
                'location' => 'gudang',
                'type' => 'out',
                'quantity' => $item['quantity'],
                'reference_type' => 'StockTransfer',
                'reference_id' => $transfer->id,
                'notes' => 'Mutasi keluar untuk transfer dapur.',
            ]);

            // Mutation into dapur
            StockMutation::create([
                'ingredient_id' => $item['ingredient_id'],
                'location' => 'dapur',
                'type' => 'in',
                'quantity' => $item['quantity'],
                'reference_type' => 'StockTransfer',
                'reference_id' => $transfer->id,
                'notes' => 'Mutasi masuk dari transfer gudang.',
            ]);
        }

        // 7. Stock Opname (Completed & Draft)
        // Opname 1: Completed
        $opname1 = StockOpname::create([
            'opname_number' => 'OPN-' . $now->copy()->subDays(4)->format('Ymd') . '-001',
            'opname_date' => $now->copy()->subDays(4)->toDateString(),
            'location' => 'gudang',
            'status' => 'completed',
            'user_id' => $userId,
            'notes' => 'Pemeriksaan rutin stock akhir bulan.',
        ]);

        $opnameItems1 = [
            [
                'stock_opname_id' => $opname1->id,
                'ingredient_id' => $ingredientModels[0]->id, // Kopi Arabica
                'system_stock' => 15000,
                'actual_stock' => 14900,
                'difference' => -100,
                'reason' => 'Selisih timbangan bubuk/biji.',
            ],
            [
                'stock_opname_id' => $opname1->id,
                'ingredient_id' => $ingredientModels[2]->id, // Susu
                'system_stock' => 48000,
                'actual_stock' => 48000,
                'difference' => 0,
                'reason' => 'Stok akurat.',
            ]
        ];

        foreach ($opnameItems1 as $oi) {
            StockOpnameItem::create($oi);
            if ($oi['difference'] != 0) {
                StockMutation::create([
                    'ingredient_id' => $oi['ingredient_id'],
                    'location' => 'gudang',
                    'type' => $oi['difference'] > 0 ? 'in' : 'out',
                    'quantity' => abs($oi['difference']),
                    'reference_type' => 'StockOpname',
                    'reference_id' => $opname1->id,
                    'notes' => 'Penyesuaian Opname: ' . $oi['reason'],
                ]);
            }
        }

        // Opname 2: Draft
        $opname2 = StockOpname::create([
            'opname_number' => 'OPN-' . $now->format('Ymd') . '-002',
            'opname_date' => $now->toDateString(),
            'location' => 'dapur',
            'status' => 'draft',
            'user_id' => $userId,
            'notes' => 'Pemeriksaan stok dapur mingguan.',
        ]);

        StockOpnameItem::create([
            'stock_opname_id' => $opname2->id,
            'ingredient_id' => $ingredientModels[0]->id,
            'system_stock' => 2000,
            'actual_stock' => 1950,
            'difference' => -50,
            'reason' => 'Tumpah sedikit saat grinding.',
        ]);

        // 8. Waste (Pembuangan Bahan)
        $waste = Waste::create([
            'waste_date' => $now->copy()->subDays(3)->toDateString(),
            'location' => 'dapur',
            'user_id' => $userId,
            'notes' => 'Susu kedaluwarsa dan roti berjamur.',
        ]);

        $wastes = [
            ['ingredient_id' => $ingredientModels[2]->id, 'quantity' => 1000], // 1L Susu basi
            ['ingredient_id' => $ingredientModels[12]->id, 'quantity' => 3],   // 3 pcs Roti berjamur
        ];

        foreach ($wastes as $w) {
            $w['waste_id'] = $waste->id;
            WasteItem::create($w);

            StockMutation::create([
                'ingredient_id' => $w['ingredient_id'],
                'location' => 'dapur',
                'type' => 'out',
                'quantity' => $w['quantity'],
                'reference_type' => 'Waste',
                'reference_id' => $waste->id,
                'notes' => 'Bahan rusak/dibuang.',
            ]);
        }
    }
}
