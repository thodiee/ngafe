<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantOption;
use App\Models\PackageItem;
use App\Models\Recipe;
use App\Models\Ingredient;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $categories = [
            'Coffee' => 'Coffee',
            'Tea' => 'Tea',
            'Food' => 'Food',
            'Bakery/Pastry' => 'Bakery/Pastry',
            'Snacks' => 'Snacks',
            'Bundling/Paket' => 'Bundling/Paket',
        ];

        $catModels = [];
        foreach ($categories as $key => $name) {
            $catModels[$key] = Category::updateOrCreate(['name' => $name]);
        }

        // Fetch ingredients for recipe mapping
        $kopiArabica = Ingredient::where('name', 'Biji Kopi Arabica')->first();
        $kopiRobusta = Ingredient::where('name', 'Biji Kopi Robusta')->first();
        $susu = Ingredient::where('name', 'Susu Segar UHT')->first();
        $gulaAren = Ingredient::where('name', 'Gula Aren Cair')->first();
        $teh = Ingredient::where('name', 'Daun Teh Premium')->first();
        $beras = Ingredient::where('name', 'Beras Cianjur')->first();
        $ayam = Ingredient::where('name', 'Daging Ayam Fillet')->first();
        $terigu = Ingredient::where('name', 'Tepung Terigu')->first();
        $telur = Ingredient::where('name', 'Telur Ayam')->first();
        $mentega = Ingredient::where('name', 'Mentega Margarin')->first();

        // 2. Products - 10 per category
        $productsData = [
            'Coffee' => [
                ['name' => 'Espresso Solo', 'price' => 15000],
                ['name' => 'Americano Classic', 'price' => 20000],
                ['name' => 'Cafe Latte Velvet', 'price' => 25000],
                ['name' => 'Cappuccino Rich', 'price' => 25000],
                ['name' => 'Caramel Macchiato Gold', 'price' => 32000],
                ['name' => 'Es Kopi Susu Gula Aren', 'price' => 22000],
                ['name' => 'Hazelnut Latte Blend', 'price' => 28000],
                ['name' => 'Vanilla Latte Creamy', 'price' => 28000],
                ['name' => 'Mochaccino Classic', 'price' => 29000],
                ['name' => 'Affogato Espresso', 'price' => 24000],
            ],
            'Tea' => [
                ['name' => 'Classic Iced Tea', 'price' => 10000],
                ['name' => 'Lemon Tea Fresh', 'price' => 15000],
                ['name' => 'Lychee Tea Special', 'price' => 18000],
                ['name' => 'Peach Tea Sweet', 'price' => 18000],
                ['name' => 'Green Tea Latte Milk', 'price' => 24000],
                ['name' => 'Thai Tea Original', 'price' => 20000],
                ['name' => 'Chamomile Hot Tea', 'price' => 15000],
                ['name' => 'Earl Grey Tea Hot', 'price' => 15000],
                ['name' => 'Jasmine Tea Classic', 'price' => 12000],
                ['name' => 'Matcha Latte Premium', 'price' => 25000],
            ],
            'Food' => [
                ['name' => 'Nasi Goreng Spesial', 'price' => 30000],
                ['name' => 'Mie Goreng Ngafe', 'price' => 28000],
                ['name' => 'Ayam Bakar Taliwang', 'price' => 38000],
                ['name' => 'Spaghetti Bolognese', 'price' => 35000],
                ['name' => 'Fettuccine Carbonara', 'price' => 37000],
                ['name' => 'Chicken Cordon Bleu', 'price' => 45000],
                ['name' => 'Nasi Gila Pedas Mantap', 'price' => 32000],
                ['name' => 'Dory Fish and Chips', 'price' => 42000],
                ['name' => 'Rice Bowl Chicken Katsu', 'price' => 34000],
                ['name' => 'Nasi Ayam Geprek Keju', 'price' => 29000],
            ],
            'Bakery/Pastry' => [
                ['name' => 'Butter Croissant', 'price' => 18000],
                ['name' => 'Chocolate Croissant', 'price' => 22000],
                ['name' => 'Cinnamon Roll Sweet', 'price' => 20000],
                ['name' => 'Almond Croissant Toast', 'price' => 25000],
                ['name' => 'Blueberry Muffin Cup', 'price' => 19000],
                ['name' => 'Chocolate Fudge Cake Slice', 'price' => 28000],
                ['name' => 'Cheese Danish Roll', 'price' => 22000],
                ['name' => 'Red Velvet Slice Cake', 'price' => 30000],
                ['name' => 'Brownies Panggang Premium', 'price' => 16000],
                ['name' => 'Garlic Bread Toast', 'price' => 15000],
            ],
            'Snacks' => [
                ['name' => 'French Fries Golden', 'price' => 18000],
                ['name' => 'Onion Rings Crispy', 'price' => 16000],
                ['name' => 'Chicken Nuggets Bites', 'price' => 20000],
                ['name' => 'Tahu Cabe Garam', 'price' => 22000],
                ['name' => 'Pisang Goreng Keju', 'price' => 20000],
                ['name' => 'Singkong Goreng Crispy', 'price' => 18000],
                ['name' => 'Cireng Bumbu Rujak', 'price' => 15000],
                ['name' => 'Garlic Parmesan Wings', 'price' => 28000],
                ['name' => 'Nachos dengan Salsa', 'price' => 25000],
                ['name' => 'Spring Rolls Veggie', 'price' => 18000],
            ],
            'Bundling/Paket' => [
                ['name' => 'Paket Sarapan Hemat', 'price' => 28000, 'is_package' => true],
                ['name' => 'Paket Ngopi Kenyang', 'price' => 45000, 'is_package' => true],
                ['name' => 'Paket Kongkow Sore', 'price' => 35000, 'is_package' => true],
                ['name' => 'Paket Sweet Duo', 'price' => 40000, 'is_package' => true],
                ['name' => 'Paket Kerja Fokus', 'price' => 30000, 'is_package' => true],
                ['name' => 'Paket Rame-Rame', 'price' => 75000, 'is_package' => true],
                ['name' => 'Paket Dinner Romantis', 'price' => 90000, 'is_package' => true],
                ['name' => 'Paket Teh & Muffin', 'price' => 30000, 'is_package' => true],
                ['name' => 'Paket Katsu Mantap', 'price' => 40000, 'is_package' => true],
                ['name' => 'Paket Double Coffee', 'price' => 70000, 'is_package' => true],
            ],
        ];

        $productModels = [];
        foreach ($productsData as $catKey => $products) {
            $catId = $catModels[$catKey]->id;
            foreach ($products as $p) {
                $p['category_id'] = $catId;
                $p['is_available'] = true;
                $p['is_package'] = $p['is_package'] ?? false;
                
                $prod = Product::updateOrCreate(['name' => $p['name']], $p);
                $productModels[$p['name']] = $prod;
            }
        }

        // 3. Product Variants & Options for Coffee/Tea/Food
        // Variant: Suhu (Hot / Ice) for Latte/Cappuccino/Tea/Green Tea
        $coffeeWithSuhu = [
            'Americano Classic', 'Cafe Latte Velvet', 'Cappuccino Rich', 
            'Caramel Macchiato Gold', 'Hazelnut Latte Blend', 'Vanilla Latte Creamy', 'Mochaccino Classic'
        ];
        foreach ($coffeeWithSuhu as $pName) {
            $p = $productModels[$pName];
            $variant = ProductVariant::create(['product_id' => $p->id, 'name' => 'Suhu', 'is_required' => true]);
            ProductVariantOption::create(['product_variant_id' => $variant->id, 'name' => 'Panas (Hot)', 'price_adjustment' => 0]);
            ProductVariantOption::create(['product_variant_id' => $variant->id, 'name' => 'Dingin (Ice)', 'price_adjustment' => 2000]); // Es lebih mahal
        }

        // Variant: Ukuran for Coffee
        $coffeeWithUkuran = ['Americano Classic', 'Cafe Latte Velvet', 'Es Kopi Susu Gula Aren'];
        foreach ($coffeeWithUkuran as $pName) {
            $p = $productModels[$pName];
            $variant = ProductVariant::create(['product_id' => $p->id, 'name' => 'Ukuran', 'is_required' => true]);
            ProductVariantOption::create(['product_variant_id' => $variant->id, 'name' => 'Regular Size', 'price_adjustment' => 0]);
            ProductVariantOption::create(['product_variant_id' => $variant->id, 'name' => 'Large Size', 'price_adjustment' => 5000]);
        }

        // Variant: Tingkat Kemanisan for Sweet Drinks
        $sweetDrinks = ['Es Kopi Susu Gula Aren', 'Thai Tea Original', 'Matcha Latte Premium', 'Green Tea Latte Milk'];
        foreach ($sweetDrinks as $pName) {
            $p = $productModels[$pName];
            $variant = ProductVariant::create(['product_id' => $p->id, 'name' => 'Kemanisan', 'is_required' => false]);
            ProductVariantOption::create(['product_variant_id' => $variant->id, 'name' => 'Normal Sugar', 'price_adjustment' => 0]);
            ProductVariantOption::create(['product_variant_id' => $variant->id, 'name' => 'Less Sugar', 'price_adjustment' => 0]);
            ProductVariantOption::create(['product_variant_id' => $variant->id, 'name' => 'No Sugar', 'price_adjustment' => 0]);
        }

        // Variant: Tingkat Pedas for Foods
        $spicyFoods = ['Nasi Goreng Spesial', 'Mie Goreng Ngafe', 'Nasi Gila Pedas Mantap', 'Nasi Ayam Geprek Keju'];
        foreach ($spicyFoods as $pName) {
            $p = $productModels[$pName];
            $variant = ProductVariant::create(['product_id' => $p->id, 'name' => 'Tingkat Pedas', 'is_required' => true]);
            ProductVariantOption::create(['product_variant_id' => $variant->id, 'name' => 'Tidak Pedas', 'price_adjustment' => 0]);
            ProductVariantOption::create(['product_variant_id' => $variant->id, 'name' => 'Sedang (Level 1)', 'price_adjustment' => 0]);
            ProductVariantOption::create(['product_variant_id' => $variant->id, 'name' => 'Pedas (Level 2)', 'price_adjustment' => 0]);
            ProductVariantOption::create(['product_variant_id' => $variant->id, 'name' => 'Sangat Pedas (Level 3)', 'price_adjustment' => 2000]);
        }

        // 4. Package Items (Linking Packages to products)
        $packagesLink = [
            'Paket Sarapan Hemat' => [
                ['name' => 'Espresso Solo', 'qty' => 1],
                ['name' => 'Butter Croissant', 'qty' => 1],
            ],
            'Paket Ngopi Kenyang' => [
                ['name' => 'Es Kopi Susu Gula Aren', 'qty' => 1],
                ['name' => 'Nasi Goreng Spesial', 'qty' => 1],
            ],
            'Paket Kongkow Sore' => [
                ['name' => 'Thai Tea Original', 'qty' => 1],
                ['name' => 'Tahu Cabe Garam', 'qty' => 1],
            ],
            'Paket Sweet Duo' => [
                ['name' => 'Matcha Latte Premium', 'qty' => 1],
                ['name' => 'Cinnamon Roll Sweet', 'qty' => 1],
            ],
            'Paket Kerja Fokus' => [
                ['name' => 'Americano Classic', 'qty' => 1],
                ['name' => 'Garlic Bread Toast', 'qty' => 1],
            ],
            'Paket Rame-Rame' => [
                ['name' => 'Es Kopi Susu Gula Aren', 'qty' => 2],
                ['name' => 'French Fries Golden', 'qty' => 1],
                ['name' => 'Garlic Parmesan Wings', 'qty' => 1],
            ],
            'Paket Dinner Romantis' => [
                ['name' => 'Spaghetti Bolognese', 'qty' => 2],
                ['name' => 'Lychee Tea Special', 'qty' => 2],
            ],
            'Paket Teh & Muffin' => [
                ['name' => 'Lemon Tea Fresh', 'qty' => 1],
                ['name' => 'Blueberry Muffin Cup', 'qty' => 1],
            ],
            'Paket Katsu Mantap' => [
                ['name' => 'Rice Bowl Chicken Katsu', 'qty' => 1],
                ['name' => 'Classic Iced Tea', 'qty' => 1],
            ],
            'Paket Double Coffee' => [
                ['name' => 'Cafe Latte Velvet', 'qty' => 2],
                ['name' => 'Chocolate Croissant', 'qty' => 2],
            ],
        ];

        foreach ($packagesLink as $pkgName => $items) {
            $package = $productModels[$pkgName];
            foreach ($items as $item) {
                PackageItem::create([
                    'package_id' => $package->id,
                    'product_id' => $productModels[$item['name']]->id,
                    'quantity' => $item['qty'],
                ]);
            }
        }

        // 5. Recipes (BOM) linking products to ingredients
        // Kopi Susu Aren
        if ($kopiArabica && $susu && $gulaAren) {
            $p = $productModels['Es Kopi Susu Gula Aren'];
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $kopiArabica->id, 'quantity' => 18]); // 18g biji kopi
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $susu->id, 'quantity' => 150]); // 150ml susu
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $gulaAren->id, 'quantity' => 20]); // 20g gula aren
        }

        // Americano
        if ($kopiArabica) {
            $p = $productModels['Americano Classic'];
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $kopiArabica->id, 'quantity' => 20]);
        }

        // Cappuccino
        if ($kopiArabica && $susu) {
            $p = $productModels['Cappuccino Rich'];
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $kopiArabica->id, 'quantity' => 18]);
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $susu->id, 'quantity' => 200]);
        }

        // Thai Tea
        if ($teh && $susu) {
            $p = $productModels['Thai Tea Original'];
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $teh->id, 'quantity' => 15]);
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $susu->id, 'quantity' => 120]);
        }

        // Nasi Goreng Spesial
        if ($beras && $ayam && $telur && $mentega) {
            $p = $productModels['Nasi Goreng Spesial'];
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $beras->id, 'quantity' => 200]); // 200g beras
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $ayam->id, 'quantity' => 50]);  // 50g ayam
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $telur->id, 'quantity' => 1]);   // 1 butir telur
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $mentega->id, 'quantity' => 10]); // 10g mentega
        }

        if ($beras && $ayam && $telur && $terigu) {
            $p = $productModels['Rice Bowl Chicken Katsu'];
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $beras->id, 'quantity' => 200]);
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $ayam->id, 'quantity' => 100]); // 100g ayam katsu
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $telur->id, 'quantity' => 1]);
            Recipe::create(['product_id' => $p->id, 'ingredient_id' => $terigu->id, 'quantity' => 30]); // 30g tepung
        }
    }
}
