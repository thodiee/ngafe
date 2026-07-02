<?php
// 1. Rewrite POS menu
$posViewPath = __DIR__ . '/resources/views/pos/menu.blade.php';
$posView = <<<'BLADE'
@extends('layouts.app')
@section('content')
<div class="flex gap-6 h-[80vh]">
    <!-- Menu Section -->
    <div class="w-2/3 flex flex-col">
        <h2 class="text-3xl font-bold mb-6 text-white"><i class="fa-solid fa-utensils text-brand-500 mr-2"></i> POS Menu</h2>
        <div class="grid grid-cols-3 gap-4 overflow-y-auto pr-2 pb-10">
            @foreach($products as $product)
            <div class="glass-dark p-4 rounded-2xl cursor-pointer hover:border-brand-500 hover:-translate-y-1 transition duration-300 group relative" onclick="handleProductClick({{ $product->toJson() }})">
                @if($product->is_package)
                    <div class="absolute top-0 right-0 bg-amber-500 text-amber-950 px-2 py-1 rounded-bl-xl rounded-tr-2xl font-bold text-[10px] z-10"><i class="fa-solid fa-box-open"></i> PAKET</div>
                @endif
                <div class="aspect-video bg-slate-800 rounded-xl mb-3 flex items-center justify-center overflow-hidden">
                    @if($product->image)
                        <img src="{{ $product->image }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    @else
                        <i class="fa-solid fa-burger text-4xl text-slate-600"></i>
                    @endif
                </div>
                <h3 class="font-semibold text-lg text-white mb-1 leading-tight">{{ $product->name }}</h3>
                <p class="text-brand-400 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                @if($product->variants->count() > 0)
                    <div class="text-[10px] text-slate-400 mt-2 bg-slate-800 inline-block px-2 py-0.5 rounded">Ada {{ $product->variants->count() }} Pilihan Varian</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Cart Section -->
    <div class="w-1/3 glass-dark p-6 rounded-3xl flex flex-col border-t-4 border-t-brand-500 shadow-2xl relative">
        <h3 class="text-xl font-bold mb-4 border-b border-slate-700 pb-2"><i class="fa-solid fa-cart-shopping mr-2"></i> Current Order</h3>
        
        <form action="{{ route('pos.order') }}" method="POST" id="orderForm" class="flex flex-col h-full">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm text-slate-400 mb-1">Select Table</label>
                <select name="table_id" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-white" required>
                    <option value="">-- Choose Table --</option>
                    @foreach($tables as $table)
                        @php
                            $reservationStr = '';
                            if($table->reservations->count() > 0) {
                                $r = $table->reservations->first();
                                $reservationStr = " (RSV: {$r->customer_name} @ {$r->reservation_time->format('H:i')})";
                            }
                        @endphp
                        <option value="{{ $table->id }}" {{ $table->reservations->count() > 0 ? 'class=text-yellow-400' : '' }}>
                            Table {{ $table->table_number }} [{{ strtoupper($table->area->name ?? 'TANPA AREA') }}] {{ $reservationStr }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm text-slate-400 mb-1">Order Type</label>
                <div class="flex gap-2">
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="order_type" value="dine_in" class="peer hidden" checked>
                        <div class="peer-checked:bg-brand-600 peer-checked:text-white bg-slate-800 p-2 rounded-lg transition"><i class="fa-solid fa-bell-concierge mr-1"></i> Dine In</div>
                    </label>
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="order_type" value="takeaway" class="peer hidden">
                        <div class="peer-checked:bg-brand-600 peer-checked:text-white bg-slate-800 p-2 rounded-lg transition"><i class="fa-solid fa-bag-shopping mr-1"></i> Takeaway</div>
                    </label>
                </div>
            </div>
            
            <div id="cartItems" class="flex-grow overflow-y-auto mb-4 space-y-2 border-t border-slate-700 pt-4">
                <div class="text-center text-slate-500 mt-10" id="emptyCart">Cart is empty</div>
            </div>
            
            <div class="border-t border-slate-700 pt-4 mb-4">
                <div class="flex justify-between items-center text-xl font-bold">
                    <span>Total</span>
                    <span id="cartTotal" class="text-brand-400">Rp 0</span>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm text-slate-400 mb-2">Payment Method</label>
                <select name="payment_method" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-white" required>
                    <option value="cash">Cash</option>
                    <option value="qris_gateway">QRIS Gateway</option>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 text-white font-bold py-4 rounded-xl shadow-lg transition active:scale-95 text-lg" id="checkoutBtn" disabled>
                Process Order
            </button>
        </form>
    </div>
</div>

<!-- Variant Selection Modal -->
<div id="variantModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4">
    <div class="bg-slate-800 rounded-2xl w-full max-w-lg shadow-2xl flex flex-col max-h-[90vh]">
        <div class="p-5 border-b border-slate-700 flex justify-between items-center">
            <h3 class="text-2xl font-bold text-white" id="modalProductName">Product Name</h3>
            <button onclick="closeVariantModal()" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <div class="p-5 overflow-y-auto flex-grow" id="variantContainer">
            <!-- Variants injected here via JS -->
        </div>
        <div class="p-5 border-t border-slate-700 bg-slate-900/50 rounded-b-2xl flex justify-between items-center">
            <div>
                <span class="text-slate-400 text-sm">Total Harga Item:</span>
                <div class="text-xl font-bold text-brand-400" id="modalItemTotal">Rp 0</div>
            </div>
            <button onclick="addSelectedVariantToCart()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3 rounded-xl font-bold transition">Tambah ke Pesanan</button>
        </div>
    </div>
</div>

<script>
    let cart = []; // Use array to support multiple same products with different variants
    let currentProduct = null;
    let selectedVariants = {};

    function handleProductClick(product) {
        if(product.variants && product.variants.length > 0) {
            openVariantModal(product);
        } else {
            addToCart(product, []);
        }
    }

    function openVariantModal(product) {
        currentProduct = product;
        selectedVariants = {}; // reset selections
        document.getElementById('modalProductName').innerText = product.name;
        
        let html = '';
        product.variants.forEach((v, vIndex) => {
            html += `<div class="mb-5">
                <h4 class="font-bold text-slate-200 mb-2">${v.name} ${v.is_required ? '<span class="text-red-400 text-xs">*Wajib</span>' : '<span class="text-slate-500 text-xs">(Opsional)</span>'}</h4>
                <div class="space-y-2">`;
            
            v.options.forEach((opt, oIndex) => {
                let inputType = v.is_required ? 'radio' : 'radio'; // keeping it radio for single choice per variant group, even if optional
                let nameAttr = `variant_${v.id}`;
                let priceText = opt.price_adjustment > 0 ? ` (+Rp${new Intl.NumberFormat('id-ID').format(opt.price_adjustment)})` : '';
                
                html += `
                <label class="flex items-center p-3 border border-slate-700 rounded-lg cursor-pointer hover:bg-slate-700/50 transition">
                    <input type="radio" name="${nameAttr}" value="${opt.id}" class="w-4 h-4 text-brand-600 bg-slate-900 border-slate-600 focus:ring-brand-600" onchange="calculateModalTotal()" ${v.is_required && oIndex === 0 ? 'checked' : ''}>
                    <span class="ml-3 text-sm font-medium text-slate-300 flex-grow">${opt.name}</span>
                    <span class="text-brand-400 text-sm font-bold" data-price="${opt.price_adjustment}">${priceText}</span>
                </label>`;
            });
            
            html += `</div></div>`;
        });
        
        document.getElementById('variantContainer').innerHTML = html;
        document.getElementById('variantModal').classList.remove('hidden');
        document.getElementById('variantModal').classList.add('flex');
        calculateModalTotal();
    }

    function closeVariantModal() {
        document.getElementById('variantModal').classList.add('hidden');
        document.getElementById('variantModal').classList.remove('flex');
        currentProduct = null;
    }

    function calculateModalTotal() {
        if(!currentProduct) return;
        let total = parseFloat(currentProduct.price);
        
        currentProduct.variants.forEach(v => {
            let selectedOpt = document.querySelector(`input[name="variant_${v.id}"]:checked`);
            if(selectedOpt) {
                let optId = selectedOpt.value;
                let option = v.options.find(o => o.id == optId);
                if(option) total += parseFloat(option.price_adjustment);
            }
        });
        
        document.getElementById('modalItemTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }

    function addSelectedVariantToCart() {
        if(!currentProduct) return;
        
        let variants = [];
        let isValid = true;
        
        currentProduct.variants.forEach(v => {
            let selectedOpt = document.querySelector(`input[name="variant_${v.id}"]:checked`);
            if(selectedOpt) {
                let optId = selectedOpt.value;
                let option = v.options.find(o => o.id == optId);
                if(option) {
                    variants.push({
                        variant_name: v.name,
                        option_name: option.name,
                        price_adjustment: option.price_adjustment
                    });
                }
            } else if (v.is_required) {
                isValid = false;
                alert(`Harap pilih opsi untuk ${v.name}`);
            }
        });
        
        if(!isValid) return;
        
        addToCart(currentProduct, variants);
        closeVariantModal();
    }
    
    function addToCart(product, variants) {
        // Calculate item total price including variants
        let itemPrice = parseFloat(product.price);
        let variantString = '';
        variants.forEach(v => {
            itemPrice += parseFloat(v.price_adjustment);
            variantString += `${v.variant_name}: ${v.option_name}|`;
        });
        
        // Find if identical product with identical variants already in cart
        let existingIndex = cart.findIndex(c => c.product.id === product.id && c.variantString === variantString);
        
        if(existingIndex >= 0) {
            cart[existingIndex].quantity++;
        } else {
            cart.push({
                product: product,
                variants: variants,
                variantString: variantString,
                itemPrice: itemPrice,
                quantity: 1
            });
        }
        updateCartUI();
    }
    
    function updateCartQuantity(index, delta) {
        if(cart[index]) {
            cart[index].quantity += delta;
            if(cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
        }
        updateCartUI();
    }
    
    function updateCartUI() {
        const container = document.getElementById('cartItems');
        const checkoutBtn = document.getElementById('checkoutBtn');
        let total = 0;
        let html = '';
        
        cart.forEach((item, index) => {
            total += item.itemPrice * item.quantity;
            
            // Build hidden inputs for form submission
            let hiddenInputs = `
                <input type="hidden" name="items[${index}][product_id]" value="${item.product.id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            `;
            
            let variantTags = '';
            item.variants.forEach((v, vIndex) => {
                hiddenInputs += `
                    <input type="hidden" name="items[${index}][variants][${vIndex}][variant_name]" value="${v.variant_name}">
                    <input type="hidden" name="items[${index}][variants][${vIndex}][option_name]" value="${v.option_name}">
                    <input type="hidden" name="items[${index}][variants][${vIndex}][price_adjustment]" value="${v.price_adjustment}">
                `;
                variantTags += `<div class="text-[10px] text-slate-400 bg-slate-900 inline-block px-1 rounded mr-1 mt-1 border border-slate-700">${v.option_name}</div>`;
            });

            html += `
                <div class="flex justify-between items-center bg-slate-800 p-3 rounded-lg animate-[fadeIn_0.3s] border border-slate-700/50">
                    ${hiddenInputs}
                    <div class="flex-1 pr-2">
                        <div class="font-semibold text-sm leading-tight text-slate-200">${item.product.name}</div>
                        ${variantTags ? `<div class="mt-1">${variantTags}</div>` : ''}
                        <div class="text-brand-400 text-xs font-bold mt-1">Rp ${new Intl.NumberFormat('id-ID').format(item.itemPrice)}</div>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-900 rounded-lg p-1">
                        <button type="button" onclick="updateCartQuantity(${index}, -1)" class="text-slate-400 hover:text-white bg-slate-700 rounded w-6 h-6 flex items-center justify-center">-</button>
                        <span class="font-bold w-4 text-center text-sm">${item.quantity}</span>
                        <button type="button" onclick="updateCartQuantity(${index}, 1)" class="text-brand-500 hover:text-white bg-slate-700 rounded w-6 h-6 flex items-center justify-center">+</button>
                    </div>
                </div>
            `;
        });
        
        if(cart.length > 0) {
            container.innerHTML = html;
            checkoutBtn.disabled = false;
        } else {
            container.innerHTML = '<div class="text-center text-slate-500 mt-10" id="emptyCart">Cart is empty</div>';
            checkoutBtn.disabled = true;
        }
        
        document.getElementById('cartTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }
</script>
<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
BLADE;
file_put_contents($posViewPath, $posView);


// 2. Update Seeder
$seederContent = file_get_contents(__DIR__ . '/database/seeders/DatabaseSeeder.php');
$seederImports = "use App\\Models\\ProductVariant;\nuse App\\Models\\ProductVariantOption;\nuse App\\Models\\PackageItem;\nuse App\\Models\\Area;\n";
$seederContent = preg_replace('/use App\\\\Models\\\\Area;\n/', $seederImports, $seederContent);

$seederBody = <<<'PHP'
        // Areas
        $areaIndoor = Area::create(['name' => 'Indoor AC']);
        $areaOutdoor = Area::create(['name' => 'Outdoor Garden']);

        // Tables
        for ($i = 1; $i <= 10; $i++) {
            Table::create([
                'table_number' => 'T' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'area_id' => $i <= 5 ? $areaIndoor->id : $areaOutdoor->id,
                'qr_token' => uniqid('qr_'),
                'status' => 'empty',
            ]);
        }

        // Ingredients
        $kopi = Ingredient::create(['name' => 'Biji Kopi Arabica', 'stock' => 5000, 'unit' => 'gram', 'minimum_stock' => 1000]);
        $susu = Ingredient::create(['name' => 'Susu Segar', 'stock' => 10000, 'unit' => 'ml', 'minimum_stock' => 2000]);
        $gula = Ingredient::create(['name' => 'Gula Aren', 'stock' => 3000, 'unit' => 'gram', 'minimum_stock' => 500]);
        $teh = Ingredient::create(['name' => 'Daun Teh Premium', 'stock' => 2000, 'unit' => 'gram', 'minimum_stock' => 500]);

        // Categories
        $catCoffee = Category::create(['name' => 'Coffee']);
        $catTea = Category::create(['name' => 'Tea']);
        $catFood = Category::create(['name' => 'Food']);
        $catPackage = Category::create(['name' => 'Bundling / Paket']);

        // Products & Recipes
        $p1 = Product::create(['name' => 'Kopi Susu Aren', 'category_id' => $catCoffee->id, 'price' => 25000]);
        Recipe::create(['product_id' => $p1->id, 'ingredient_id' => $kopi->id, 'quantity' => 18]);
        Recipe::create(['product_id' => $p1->id, 'ingredient_id' => $susu->id, 'quantity' => 150]);
        Recipe::create(['product_id' => $p1->id, 'ingredient_id' => $gula->id, 'quantity' => 20]);

        // Variants for P1
        $var1 = ProductVariant::create(['product_id' => $p1->id, 'name' => 'Suhu', 'is_required' => true]);
        ProductVariantOption::create(['product_variant_id' => $var1->id, 'name' => 'Dingin (Ice)', 'price_adjustment' => 0]);
        ProductVariantOption::create(['product_variant_id' => $var1->id, 'name' => 'Panas (Hot)', 'price_adjustment' => 0]);

        $var2 = ProductVariant::create(['product_id' => $p1->id, 'name' => 'Ukuran', 'is_required' => true]);
        ProductVariantOption::create(['product_variant_id' => $var2->id, 'name' => 'Regular', 'price_adjustment' => 0]);
        ProductVariantOption::create(['product_variant_id' => $var2->id, 'name' => 'Large', 'price_adjustment' => 5000]);

        $p2 = Product::create(['name' => 'Americano', 'category_id' => $catCoffee->id, 'price' => 20000]);
        Recipe::create(['product_id' => $p2->id, 'ingredient_id' => $kopi->id, 'quantity' => 20]);
        
        $var3 = ProductVariant::create(['product_id' => $p2->id, 'name' => 'Suhu', 'is_required' => true]);
        ProductVariantOption::create(['product_variant_id' => $var3->id, 'name' => 'Dingin (Ice)', 'price_adjustment' => 0]);
        ProductVariantOption::create(['product_variant_id' => $var3->id, 'name' => 'Panas (Hot)', 'price_adjustment' => 0]);

        $p3 = Product::create(['name' => 'Thai Tea', 'category_id' => $catTea->id, 'price' => 22000]);
        Recipe::create(['product_id' => $p3->id, 'ingredient_id' => $teh->id, 'quantity' => 15]);
        Recipe::create(['product_id' => $p3->id, 'ingredient_id' => $susu->id, 'quantity' => 100]);

        // Nasi Goreng
        $p4 = Product::create(['name' => 'Nasi Goreng Spesial', 'category_id' => $catFood->id, 'price' => 30000]);
        $var4 = ProductVariant::create(['product_id' => $p4->id, 'name' => 'Tingkat Pedas', 'is_required' => true]);
        ProductVariantOption::create(['product_variant_id' => $var4->id, 'name' => 'Tidak Pedas', 'price_adjustment' => 0]);
        ProductVariantOption::create(['product_variant_id' => $var4->id, 'name' => 'Sedang', 'price_adjustment' => 0]);
        ProductVariantOption::create(['product_variant_id' => $var4->id, 'name' => 'Sangat Pedas', 'price_adjustment' => 0]);

        // Package Example
        $pkg1 = Product::create(['name' => 'Paket Ngopi Kenyang', 'category_id' => $catPackage->id, 'price' => 50000, 'is_package' => true]);
        PackageItem::create(['package_id' => $pkg1->id, 'product_id' => $p1->id, 'quantity' => 1]); // Kopsus
        PackageItem::create(['package_id' => $pkg1->id, 'product_id' => $p4->id, 'quantity' => 1]); // Nasgor

PHP;

// Find the section to replace: from '// Tables' to '// Job Vacancies'
$pattern = '/\/\/\s*Tables.*?\/\/\s*Job Vacancies/s';
$seederContent = preg_replace($pattern, $seederBody . "\n        // Job Vacancies", $seederContent);

file_put_contents(__DIR__ . '/database/seeders/DatabaseSeeder.php', $seederContent);

echo "Rewritten pos menu and seeder.\n";
