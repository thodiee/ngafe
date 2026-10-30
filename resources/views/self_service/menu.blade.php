@extends('layouts.app')

@section('content')
@php
$firstCategorySlug = $categories->first() ? Str::slug($categories->first()->name) : 'all';

$taxEnabled = \App\Models\Setting::get('tax_enabled', '0') === '1';
$taxRate = (float) \App\Models\Setting::get('tax_rate', 11);
$taxLabel = \App\Models\Setting::get('tax_label', 'PPN');
$taxInclusive = \App\Models\Setting::get('tax_inclusive', '0') === '1';
$serviceEnabled = \App\Models\Setting::get('service_charge_enabled', '0') === '1';
$serviceRate = (float) \App\Models\Setting::get('service_charge_rate', 5);

$categoryImages = [
    'Coffee' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500&auto=format&fit=crop&q=80',
    'Tea' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=500&auto=format&fit=crop&q=80',
    'Food' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=80',
    'Bakery/Pastry' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=500&auto=format&fit=crop&q=80',
    'Snacks' => 'https://images.unsplash.com/photo-1599490659213-e2b9527bb087?w=500&auto=format&fit=crop&q=80',
    'Bundling/Paket' => 'https://images.unsplash.com/photo-1513267048331-5611cad2134b?w=500&auto=format&fit=crop&q=80',
    'default' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500&auto=format&fit=crop&q=80'
];
@endphp

<div class="max-w-md mx-auto space-y-6 pb-24 relative">
    <!-- Table Banner Header -->
    <div class="bg-gradient-to-r from-brand-500 to-brand-600 rounded-3xl p-6 text-white shadow-xl shadow-brand-500/10 border border-brand-500/20 flex items-center justify-between">
        <div class="space-y-1">
            <span class="text-xs text-brand-100 font-bold uppercase tracking-wider">Self-Service Order</span>
            <h2 class="text-3xl font-extrabold tracking-tight">MEJA {{ $table->table_number }}</h2>
            <p class="text-xs text-brand-100/80">{{ $table->area->name ?? 'Kafe Utama' }}</p>
        </div>
        <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20">
            <i class="fa-solid fa-mobile-screen-button text-3xl"></i>
        </div>
    </div>

    <!-- Customer Details Form -->
    <div class="glass-luxury p-5 rounded-3xl border border-brand-500/10 dark:border-brand-500/5 space-y-3">
        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Nama Pemesan <span class="text-red-500">*</span></label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                <i class="fa-solid fa-user"></i>
            </div>
            <input type="text" id="customer_name" class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-slate-800 dark:text-white focus:border-brand-500 outline-none transition text-sm" placeholder="Masukkan nama Anda (misal: Budi)" required>
        </div>
    </div>

    <!-- Category Tabs (Horizontal Scrollable) -->
    <div class="flex gap-2 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-none">
        <button onclick="filterCategory('all')" 
            id="tab-all"
            class="category-tab whitespace-nowrap px-5 py-2.5 rounded-full text-xs font-bold transition-all duration-300 {{ $firstCategorySlug === 'all' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-brand-50 dark:hover:bg-slate-700/50' }}">
            Semua Menu
        </button>
        @foreach($categories as $category)
            @php
                $slug = Str::slug($category->name);
                $isActive = $slug === $firstCategorySlug;
            @endphp
            <button onclick="filterCategory('{{ $slug }}')" 
                id="tab-{{ $slug }}"
                class="category-tab whitespace-nowrap px-5 py-2.5 rounded-full text-xs font-bold transition-all duration-300 {{ $isActive ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-brand-50 dark:hover:bg-slate-700/50' }}">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    <!-- Product Grid list (2-Column Grid Layout) -->
    <div class="grid grid-cols-2 gap-3" id="products-list">
        @foreach($categories as $category)
            @foreach($category->products as $product)
                @php 
                    $imageSrc = $product->image ? asset('storage/' . $product->image) : ($categoryImages[$category->name] ?? $categoryImages['default']);
                @endphp
                <!-- Product Card Grid Item -->
                <div class="product-card glass-luxury p-3 rounded-2xl border border-brand-500/10 dark:border-brand-500/5 flex flex-col justify-between hover:-translate-y-1 hover:shadow-md transition-all duration-300 relative group cursor-pointer"
                    data-category="{{ Str::slug($category->name) }}"
                    onclick="handleProductCardClick(event, {{ json_encode($product) }})">
                    
                    <!-- Thumbnail -->
                    <div class="w-full aspect-square rounded-xl overflow-hidden bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 relative mb-2 flex-shrink-0">
                        <img src="{{ $imageSrc }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @if($product->is_package)
                            <span class="absolute bottom-1 right-1 bg-brand-500 text-white font-bold text-[8px] px-1.5 py-0.5 rounded-md shadow-sm"><i class="fa-solid fa-gift mr-0.5"></i> Paket</span>
                        @endif
                    </div>
                    
                    <!-- Content info -->
                    <div class="flex-grow flex flex-col justify-between">
                        <div class="space-y-0.5">
                            <h3 class="font-bold text-xs text-slate-800 dark:text-white line-clamp-2 leading-tight group-hover:text-brand-500 transition-colors">
                                {{ $product->name }}
                            </h3>
                            <span class="text-[9px] text-slate-400 dark:text-slate-500 font-medium block">
                                {{ $category->name }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between mt-1.5">
                            <p class="font-extrabold text-brand-600 dark:text-brand-400 text-xs">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                            
                            <!-- Add to Cart Visual Indicator -->
                            <div class="w-6 h-6 rounded-lg bg-brand-500/10 text-brand-500 group-hover:bg-brand-500 group-hover:text-white flex items-center justify-center transition-all duration-300">
                                @if($product->variants->count() > 0)
                                    <i class="fa-solid fa-ellipsis text-[10px]"></i>
                                @else
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>

    <!-- Variant Selector Modal -->
    <div id="variantModal" class="fixed inset-0 bg-black/60 hidden items-end sm:items-center justify-center z-50 flex p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 w-full max-w-md p-6 rounded-t-3xl sm:rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl space-y-6 animate-fade-in-up">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white" id="modalProductName">Nama Produk</h3>
                    <p class="text-xs text-slate-400" id="modalProductCategory">Kategori</p>
                </div>
                <button onclick="closeVariantModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Note Input inside variant selection -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-500">Catatan Khusus (Opsional)</label>
                <input type="text" id="modalNotes" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-800 dark:text-white outline-none focus:border-brand-500" placeholder="Contoh: Kurang manis, tidak pakai es">
            </div>

            <!-- Dynamic Variant Groups list -->
            <div id="modalVariantGroups" class="space-y-4 max-h-[30vh] overflow-y-auto pr-1">
                <!-- Javascript will inject lists here -->
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 pt-4">
                <div class="space-y-0.5">
                    <span class="text-xs text-slate-400">Harga Varian</span>
                    <p class="font-extrabold text-brand-600 dark:text-brand-400 text-lg" id="modalTotalPrice">Rp 0</p>
                </div>
                <button onclick="addSelectedVariantToCart()" class="bg-brand-500 hover:bg-brand-600 text-white px-6 py-3 rounded-2xl font-bold transition shadow-md shadow-brand-500/10 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-cart-plus"></i> Tambah Ke Keranjang
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Cart Bar at the bottom -->
    <div id="floatingCart" class="fixed bottom-4 inset-x-4 max-w-md mx-auto bg-slate-900 dark:bg-[#121218] border border-slate-800 text-white rounded-3xl p-4 flex items-center justify-between shadow-2xl z-40 transition-all duration-300 translate-y-24 opacity-0" style="display: none;">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-brand-500/10 text-brand-500 flex items-center justify-center text-lg relative">
                <i class="fa-solid fa-shopping-bag"></i>
                <span id="floatingCartCount" class="absolute -top-1.5 -right-1.5 bg-brand-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-slate-900">0</span>
            </div>
            <div class="space-y-0.5">
                <span class="text-[10px] text-slate-400 block leading-tight">Total Keranjang</span>
                <p id="floatingCartTotal" class="font-extrabold text-sm text-brand-400">Rp 0</p>
            </div>
        </div>
        <button onclick="openCartDetails()" class="bg-brand-500 hover:bg-brand-600 text-white font-bold text-xs px-5 py-2.5 rounded-2xl transition shadow-md shadow-brand-500/10 flex items-center gap-1.5">
            Lanjut Bayar <i class="fa-solid fa-arrow-right"></i>
        </button>
    </div>

    <!-- Bottom Sheet Cart Details Modal -->
    <div id="cartSheet" class="fixed inset-0 bg-black/60 hidden items-end justify-center z-50 flex p-0 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 w-full max-w-md p-6 rounded-t-3xl border-t border-slate-200 dark:border-slate-800 shadow-2xl space-y-6 animate-fade-in-up">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-shopping-cart text-brand-500"></i>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Detail Pesanan Anda</h3>
                </div>
                <button onclick="closeCartDetails()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Cart list -->
            <div id="cartList" class="space-y-3 max-h-[30vh] overflow-y-auto pr-1">
                <!-- Javascript will inject list of items here -->
            </div>

            <!-- Payment Methods selector -->
            <div class="space-y-3 bg-slate-50 dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                <h4 class="text-xs font-bold text-slate-500">Metode Pembayaran</h4>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 cursor-pointer hover:border-brand-500 transition relative">
                        <input type="radio" name="payment_method" value="cashier" class="absolute top-2.5 right-2.5 text-brand-600 focus:ring-brand-500" checked>
                        <i class="fa-solid fa-cash-register text-slate-500 text-lg mb-1"></i>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Bayar di Kasir</span>
                    </label>
                    
                    <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 cursor-pointer hover:border-brand-500 transition relative">
                        <input type="radio" name="payment_method" value="qris" class="absolute top-2.5 right-2.5 text-brand-600 focus:ring-brand-500">
                        <i class="fa-solid fa-qrcode text-brand-500 text-lg mb-1"></i>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">QRIS / Online</span>
                    </label>
                </div>
            </div>

            <!-- Summary price -->
            <div class="space-y-2 border-t border-slate-100 dark:border-slate-800 pt-4">
                <div class="flex justify-between text-xs text-slate-400">
                    <span>Jumlah Barang</span>
                    <span id="cartSheetCount" class="font-bold text-slate-700 dark:text-slate-300">0 Item</span>
                </div>
                
                <!-- Calculation Breakdown Container -->
                <div id="cartSummaryBreakdown" class="space-y-1.5 py-2 border-y border-slate-100 dark:border-slate-800 my-2">
                    <!-- Tax and Service Charge calculations injected here -->
                </div>

                <div class="flex justify-between text-sm font-bold text-[#2c2520] dark:text-white">
                    <span>Total Pembayaran</span>
                    <span id="cartSheetTotal" class="text-brand-600 dark:text-brand-400 text-base">Rp 0</span>
                </div>
            </div>

            <!-- Submit Button -->
            <button onclick="submitOrder()" id="btnSubmitOrder" class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-3.5 rounded-2xl transition flex items-center justify-center gap-2 shadow-lg shadow-brand-500/10 text-sm">
                <i class="fa-solid fa-paper-plane"></i> Kirim Pesanan Sekarang
            </button>
        </div>
    </div>
</div>

<!-- Category Filter & Cart JS script -->
<script>
let currentCategory = '{{ $firstCategorySlug }}';
let cart = [];
let currentProduct = null;

// Tax & Service settings
const taxEnabled = {{ $taxEnabled ? 'true' : 'false' }};
const taxRate = {{ $taxRate }};
const taxLabel = '{{ $taxLabel }}';
const taxInclusive = {{ $taxInclusive ? 'true' : 'false' }};
const serviceEnabled = {{ $serviceEnabled ? 'true' : 'false' }};
const serviceRate = {{ $serviceRate }};

// Set initial view on load
document.addEventListener('DOMContentLoaded', () => {
    filterCategory(currentCategory);
});

function filterCategory(categorySlug) {
    currentCategory = categorySlug;
    
    // Update active tab styles
    document.querySelectorAll('.category-tab').forEach(btn => {
        btn.classList.remove('bg-brand-500', 'text-white', 'shadow-md', 'shadow-brand-500/20');
        btn.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300', 'border', 'border-slate-200', 'dark:border-slate-700');
    });

    const activeBtn = document.getElementById('tab-' + categorySlug);
    if (activeBtn) {
        activeBtn.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300', 'border', 'border-slate-200', 'dark:border-slate-700');
        activeBtn.classList.add('bg-brand-500', 'text-white', 'shadow-md', 'shadow-brand-500/20');
    }

    // Filter cards
    document.querySelectorAll('.product-card').forEach(card => {
        if (categorySlug === 'all' || card.getAttribute('data-category') === categorySlug) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function handleProductCardClick(event, product) {
    // If click was on some inner buttons, we don't double trigger (though currently there are no direct buttons, just visual wrappers)
    if (product.variants && product.variants.length > 0) {
        openVariantModal(product);
    } else {
        addToCartDirectly(product);
    }
}

// Variant Modal Handling
function openVariantModal(product) {
    currentProduct = product;
    document.getElementById('modalProductName').innerText = product.name;
    document.getElementById('modalProductCategory').innerText = product.category ? product.category.name : '';
    document.getElementById('modalNotes').value = '';
    
    const container = document.getElementById('modalVariantGroups');
    container.innerHTML = '';
    
    product.variants.forEach(variant => {
        let optionsHtml = '';
        variant.options.forEach((opt, idx) => {
            optionsHtml += `
                <label class="flex items-center justify-between p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 cursor-pointer hover:border-brand-500/50 transition">
                    <div class="flex items-center gap-2">
                        <input type="radio" name="variant_${variant.id}" value="${opt.id}" data-name="${opt.name}" data-price="${opt.price_adjustment}" class="text-brand-600 focus:ring-brand-500" ${idx === 0 ? 'checked' : ''} onchange="updateModalPrice()">
                        <span class="text-xs font-medium text-slate-700 dark:text-slate-300">${opt.name}</span>
                    </div>
                    ${opt.price_adjustment > 0 ? `<span class="text-[10px] text-brand-600 font-bold font-mono">+ Rp ${new Intl.NumberFormat('id-ID').format(opt.price_adjustment)}</span>` : ''}
                </label>
            `;
        });
        
        container.innerHTML += `
            <div class="space-y-2 variant-group" data-variant-name="${variant.name}" data-variant-id="${variant.id}">
                <span class="text-xs font-bold text-slate-500 block">${variant.name}</span>
                <div class="grid grid-cols-1 gap-2">
                    ${optionsHtml}
                </div>
            </div>
        `;
    });
    
    updateModalPrice();
    document.getElementById('variantModal').classList.remove('hidden');
    document.getElementById('variantModal').classList.add('flex');
}

function closeVariantModal() {
    document.getElementById('variantModal').classList.add('hidden');
    document.getElementById('variantModal').classList.remove('flex');
    currentProduct = null;
}

function updateModalPrice() {
    if (!currentProduct) return;
    let totalPrice = parseFloat(currentProduct.price);
    
    document.querySelectorAll('#modalVariantGroups input[type="radio"]:checked').forEach(radio => {
        totalPrice += parseFloat(radio.getAttribute('data-price') || 0);
    });
    
    document.getElementById('modalTotalPrice').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice);
}

function addToCartDirectly(product) {
    addToCart(product, [], null);
}

function addSelectedVariantToCart() {
    if (!currentProduct) return;
    
    let variants = [];
    document.querySelectorAll('#modalVariantGroups input[type="radio"]:checked').forEach(radio => {
        const group = radio.closest('.variant-group');
        variants.push({
            variant_name: group.getAttribute('data-variant-name'),
            option_name: radio.getAttribute('data-name'),
            price_adjustment: parseFloat(radio.getAttribute('data-price') || 0)
        });
    });
    
    const notes = document.getElementById('modalNotes').value.trim();
    addToCart(currentProduct, variants, notes);
    closeVariantModal();
}

function addToCart(product, variants, notes) {
    const variantString = JSON.stringify(variants);
    
    // Check if duplicate item exists
    const idx = cart.findIndex(item => item.product.id === product.id && JSON.stringify(item.variants) === variantString && item.notes === notes);
    
    if (idx >= 0) {
        cart[idx].quantity++;
    } else {
        cart.push({
            product: product,
            variants: variants,
            quantity: 1,
            notes: notes
        });
    }
    
    updateCartUI();
}

function updateCartQuantity(index, delta) {
    if (cart[index]) {
        cart[index].quantity += delta;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
    }
    updateCartUI();
}

function updateCartUI() {
    let totalCount = 0;
    let totalPrice = 0;
    
    cart.forEach(item => {
        totalCount += item.quantity;
        let itemPrice = parseFloat(item.product.price);
        item.variants.forEach(v => {
            itemPrice += parseFloat(v.price_adjustment);
        });
        totalPrice += itemPrice * item.quantity;
    });
    
    // Update floating bar
    const floatingCart = document.getElementById('floatingCart');
    if (totalCount > 0) {
        floatingCart.style.display = 'flex';
        // Force reflow to trigger transition
        floatingCart.offsetHeight;
        floatingCart.classList.remove('translate-y-24', 'opacity-0');
        floatingCart.classList.add('translate-y-0', 'opacity-100');
        document.getElementById('floatingCartCount').innerText = totalCount;
        document.getElementById('floatingCartTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice);
    } else {
        floatingCart.classList.remove('translate-y-0', 'opacity-100');
        floatingCart.classList.add('translate-y-24', 'opacity-0');
        setTimeout(() => {
            if (cart.length === 0) {
                floatingCart.style.display = 'none';
            }
        }, 300);
        closeCartDetails();
    }
}

// Bottom Sheet Cart UI
function openCartDetails() {
    const container = document.getElementById('cartList');
    container.innerHTML = '';
    
    let totalCount = 0;
    let subtotal = 0;
    
    cart.forEach((item, index) => {
        totalCount += item.quantity;
        let itemPrice = parseFloat(item.product.price);
        let variantLabels = [];
        
        item.variants.forEach(v => {
            itemPrice += parseFloat(v.price_adjustment);
            variantLabels.push(v.option_name);
        });
        
        const variantsStr = variantLabels.length > 0 ? variantLabels.join(', ') : '';
        const noteStr = item.notes ? `<p class="text-[10px] text-orange-500 font-medium italic mt-0.5"><i class="fa-solid fa-comment mr-1"></i>"${item.notes}"</p>` : '';
        const finalPrice = itemPrice * item.quantity;
        subtotal += finalPrice;
        
        container.innerHTML += `
            <div class="flex items-center justify-between p-3 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                <div class="space-y-0.5 flex-grow pr-3">
                    <h4 class="font-bold text-xs text-slate-800 dark:text-white line-clamp-1">${item.product.name}</h4>
                    ${variantsStr ? `<span class="text-[9px] text-slate-400 block">${variantsStr}</span>` : ''}
                    ${noteStr}
                    <p class="text-xs font-bold text-brand-600 dark:text-brand-400 mt-1">Rp ${new Intl.NumberFormat('id-ID').format(itemPrice)}</p>
                </div>
                <div class="flex items-center gap-2.5">
                    <button onclick="updateCartQuantity(${index}, -1)" class="w-6 h-6 rounded bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center font-bold text-xs">-</button>
                    <span class="text-xs font-bold text-slate-800 dark:text-white font-mono">${item.quantity}</span>
                    <button onclick="updateCartQuantity(${index}, 1)" class="w-6 h-6 rounded bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center font-bold text-xs">+</button>
                </div>
            </div>
        `;
    });
    
    // Calculate tax and service charge
    let serviceCharge = 0;
    if (serviceEnabled) {
        serviceCharge = subtotal * (serviceRate / 100);
    }
    
    let taxAmount = 0;
    if (taxEnabled && !taxInclusive) {
        taxAmount = (subtotal + serviceCharge) * (taxRate / 100);
    }
    
    const grandTotal = subtotal + serviceCharge + taxAmount;
    
    // Injects breakdown into cart sheet summary
    const summaryContainer = document.getElementById('cartSummaryBreakdown');
    if (summaryContainer) {
        let breakdownHtml = `
            <div class="flex justify-between text-xs text-slate-400">
                <span>Subtotal</span>
                <span class="font-semibold text-slate-700 dark:text-slate-300">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</span>
            </div>
        `;
        
        if (serviceEnabled) {
            breakdownHtml += `
                <div class="flex justify-between text-xs text-slate-400">
                    <span>Biaya Layanan (${serviceRate}%)</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">Rp ${new Intl.NumberFormat('id-ID').format(serviceCharge)}</span>
                </div>
            `;
        }
        
        if (taxEnabled) {
            breakdownHtml += `
                <div class="flex justify-between text-xs text-slate-400">
                    <span>${taxLabel} (${taxRate}%)${taxInclusive ? ' (Sudah Termasuk)' : ''}</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">Rp ${new Intl.NumberFormat('id-ID').format(taxAmount)}</span>
                </div>
            `;
        }
        
        summaryContainer.innerHTML = breakdownHtml;
    }
    
    document.getElementById('cartSheetCount').innerText = totalCount + ' Item';
    document.getElementById('cartSheetTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
    
    document.getElementById('cartSheet').classList.remove('hidden');
    document.getElementById('cartSheet').classList.add('flex');
}

function closeCartDetails() {
    document.getElementById('cartSheet').classList.add('hidden');
    document.getElementById('cartSheet').classList.remove('flex');
}

// Submit Order to backend via AJAX
function submitOrder() {
    const nameInput = document.getElementById('customer_name');
    const customerName = nameInput.value.trim();
    
    if (!customerName) {
        alert('Mohon masukkan nama pemesan terlebih dahulu!');
        nameInput.focus();
        closeCartDetails();
        return;
    }
    
    if (cart.length === 0) {
        alert('Keranjang belanja Anda kosong!');
        return;
    }
    
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const btn = document.getElementById('btnSubmitOrder');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin"></i> Memproses Pesanan...';
    
    // Compile items array for POST
    const itemsData = cart.map(item => {
        return {
            product_id: item.product.id,
            quantity: item.quantity,
            notes: item.notes,
            variants: item.variants
        };
    });
    
    fetch('{{ route("self-service.order.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            table_id: '{{ $table->id }}',
            customer_name: customerName,
            items: itemsData,
            payment_method: paymentMethod
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            // Clear cart
            cart = [];
            updateCartUI();
            
            // Redirect
            window.location.href = data.redirect_url;
        } else {
            alert('Gagal membuat pesanan: ' + (data.message || 'Error tidak diketahui'));
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Kirim Pesanan Sekarang';
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan koneksi server: ' + (err.message || 'Silakan coba lagi.'));
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Kirim Pesanan Sekarang';
    });
}
</script>
@endsection
